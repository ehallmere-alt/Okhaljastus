<?php
// O.K Haljastus: hinnapäringu vorm. Salvestab päringu faili data/leads.json ja saadab kirja.
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

// Kuhu päringud saadetakse. Tühi = ainult salvestatakse faili data/leads.json.
const LEAD_EMAIL = '';
const DATA_DIR = __DIR__ . '/data';
const RATE_MAX = 5;          // päringut
const RATE_WINDOW = 600;     // sekundis (10 min) ühe IP kohta
const LEADS_KEEP = 1000;

function reply(int $code, array $body): void
{
    http_response_code($code);
    echo json_encode($body);
    exit;
}

function clean(string $v, int $max): string
{
    $v = trim(str_replace(["\r", "\n"], ' ', strip_tags($v)));
    return mb_substr($v, 0, $max);
}

/** Loeb JSON-faili lukuga, laseb $fn-il seda muuta ja kirjutab tagasi. */
function with_json_file(string $file, callable $fn)
{
    $fh = @fopen($file, 'c+');
    if (!$fh) {
        return null;
    }
    $result = null;
    if (flock($fh, LOCK_EX)) {
        $all = json_decode((string)stream_get_contents($fh), true);
        [$all, $result] = $fn(is_array($all) ? $all : []);
        ftruncate($fh, 0);
        rewind($fh);
        fwrite($fh, json_encode($all, JSON_UNESCAPED_UNICODE));
        fflush($fh);
        flock($fh, LOCK_UN);
    }
    fclose($fh);
    return $result;
}

function rate_limited(): bool
{
    $key = hash('sha256', (string)($_SERVER['REMOTE_ADDR'] ?? ''));
    $now = time();
    $limited = with_json_file(DATA_DIR . '/rate_send.json', function (array $all) use ($key, $now) {
        foreach ($all as $k => $times) {
            $all[$k] = array_values(array_filter((array)$times, fn($t) => $t > $now - RATE_WINDOW));
            if (!$all[$k]) {
                unset($all[$k]);
            }
        }
        $mine = $all[$key] ?? [];
        if (count($mine) >= RATE_MAX) {
            return [$all, true];
        }
        $all[$key] = [...$mine, $now];
        return [$all, false];
    });
    return $limited === true; // faili ei saa kirjutada: ära blokeeri päris kliente
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    reply(405, ['ok' => false]);
}
// Rämpsposti lõks: peidetud väli peab olema tühi
if (!empty($_POST['website'])) {
    reply(200, ['ok' => true]);
}
if (!is_dir(DATA_DIR)) {
    @mkdir(DATA_DIR, 0750, true);
}
if (rate_limited()) {
    reply(429, ['ok' => false, 'error' => 'rate']);
}

$name = clean((string)($_POST['name'] ?? ''), 100);
$phone = clean((string)($_POST['phone'] ?? ''), 40);
$message = mb_substr(trim(strip_tags((string)($_POST['message'] ?? ''))), 0, 2000);

if ($name === '' || !preg_match('/^[0-9+()\s-]{5,40}$/', $phone)) {
    reply(422, ['ok' => false, 'error' => 'invalid']);
}

$lead = ['id' => bin2hex(random_bytes(6)), 'time' => date('c'), 'name' => $name, 'phone' => $phone, 'message' => $message];
$saved = with_json_file(DATA_DIR . '/leads.json', fn(array $all) => [array_slice([$lead, ...$all], 0, LEADS_KEEP), true]) === true;

$mailed = false;
if (filter_var(LEAD_EMAIL, FILTER_VALIDATE_EMAIL)) {
    $host = preg_replace('/[^a-z0-9.-]/i', '', (string)($_SERVER['HTTP_HOST'] ?? 'localhost'));
    $subject = '=?UTF-8?B?' . base64_encode('Uus hinnapäring: ' . $name) . '?=';
    $body = "Nimi: $name\nTelefon: $phone\n\n" . ($message !== '' ? $message : '-') . "\n";
    $headers = "From: O.K Haljastus <noreply@$host>\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n";
    $mailed = @mail(LEAD_EMAIL, $subject, $body, $headers);
}

if (!$saved && !$mailed) {
    error_log('O.K Haljastus send.php: lead not saved and not mailed');
    reply(500, ['ok' => false]);
}
reply(200, ['ok' => true]);
