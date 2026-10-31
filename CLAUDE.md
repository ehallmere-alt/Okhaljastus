# O.K Haljastus

Landing page for O.K Haljastus, a landscaping company (thuja planting, garden work) in Tiskre, Estonia.
Phone `+372 565 1074`, address `Oja tee 10, Tiskre küla`. Site language: **Estonian** (`lang="et"`).

## Talking to the owner

- Reply in **Russian**, plainly and honestly. The owner is not a developer: explain changes in simple words.
- Commit and push to the session branch when the work is done. No PRs unless asked.

## Current state

One-page site: `index.html`, `style.css`, `script.js`, `send.php`.

- Big `tel:` buttons (hero, header, floating "Helista" on mobile): calls are the main lead channel.
- Quote form (name, phone, optional message) posts via `fetch` to `send.php`: honeypot field `website`,
  rate limit 5 / 10 min per IP, each lead saved to `data/leads.json` (newest first, max 1000).
- `LEAD_EMAIL` at the top of `send.php` is **empty**: leads are only saved to the file until the owner gives an email.
  The owner reads leads by downloading `data/leads.json` over FTP. No admin panel yet (build one only if asked).
- `data/.htaccess` blocks web access to `data/`. `data/*.json` is gitignored.
- Still placeholders: text logo instead of `logo.png` (commented `<img>` is ready in `index.html`), Unsplash gallery
  photos (the sandbox can't load Unsplash, so they look empty in local screenshots; that's expected).
- Hosting: same provider as crestudio.ee (PHP available) but a **separate FTP account**.

## Commands

No build step, no dependencies. Plain HTML/CSS/JS.

```bash
# Local preview (PHP needed for the form)
php -S 127.0.0.1:8098              # then open http://127.0.0.1:8098/

# Checks
php -l send.php
for f in / style.css script.js; do curl -s -o /dev/null -w "%{http_code} $f\n" "127.0.0.1:8098/$f"; done
curl -s -d "name=Test&phone=55555555" 127.0.0.1:8098/send.php   # {"ok":true}
rm -f data/*.json                  # remove test leads afterwards
```

No test suite. "Tested" = page and assets return 200 and it looks right on mobile and desktop
(Chromium + Playwright are preinstalled for screenshots). The generic `.claude/rules/` TDD / 80% coverage /
TypeScript rules do not apply here.

## Code style

- Plain, semantic HTML5; no frameworks, no build tools unless the owner asks.
- Mobile first: most visitors come from phones. The phone number should be a `tel:` link.
- Images: real `alt` text in Estonian, `width`/`height`, `loading="lazy"`, WebP where possible.
- Basic SEO on every page: `<title>`, meta description, OG tags, LocalBusiness JSON-LD with phone and address.
- Two-space indentation in HTML/CSS/JS (matches `index.html`).
- Commit messages: short English summary line, optional bullet list.
