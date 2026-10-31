# O.K Haljastus

Landing page for O.K Haljastus, a landscaping company (thuja planting, garden work) in Tiskre, Estonia.
Phone `+372 565 1074`, address `Oja tee 10, Tiskre küla`. Site language: **Estonian** (`lang="et"`).

## Talking to the owner

- Reply in **Russian**, plainly and honestly. The owner is not a developer: explain changes in simple words.
- Commit and push to the session branch when the work is done. No PRs unless asked.

## Current state

**The old site was removed on purpose** (owner's decision, 2026-10-31). It stays in git history only.
The **new** O.K Haljastus site exists only on the hosting for now; the owner will download it over FTP and add it here.

When the new files arrive:
- Make sure no secrets got committed (`config.php` with DB passwords, `.env`, `wp-config.php`, lead/data JSON). If any did, remove them from the repo and tell the owner to change those passwords.
- The new site is **plain PHP/HTML with its own custom admin panel** (not WordPress, owner confirmed). Probably built
  like crestudio.ee (content in JSON under `data/`, admin with password hash). Read it and rewrite this file: structure, commands, deploy.
- **The GitHub repo is public.** Admin password hashes, leads, client data and any `data/*.json` written by the admin
  must be gitignored and never committed (same rule as crestudio.ee). If they were uploaded, delete them from the repo
  and tell the owner to change the admin password.
- Production data on the hosting is newer than git: never tell the owner to upload `data/` back over it.
- The owner wants: big `tel:` call buttons (calls are the main lead channel) and a working quote form.
  A tested implementation (send.php with honeypot, rate limit, leads saved to `data/leads.json`, `data/.htaccess` deny)
  is in git history: commit `0913318`. Reuse it if the new site has no working form.
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
