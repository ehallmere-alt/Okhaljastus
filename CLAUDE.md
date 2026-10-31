# O.K Haljastus

Landing page for O.K Haljastus, a landscaping company (thuja planting, garden work) in Tiskre, Estonia.
Phone `+372 565 1074`, address `Oja tee 10, Tiskre küla`. Site language: **Estonian** (`lang="et"`).

## Talking to the owner

- Reply in **Russian**, plainly and honestly. The owner is not a developer: explain changes in simple words.
- Commit and push to the session branch when the work is done. No PRs unless asked.

## Current state

Only `index.html` exists. It links to `style.css`, `script.js` and `logo.png`, which are **not in the repo yet**
(`scrollToForm()` is called but not defined). Gallery photos are Unsplash placeholders. The quote form
(name + phone) has no backend: it sends nothing.

Form delivery is **not decided yet**. Recommended option (proposed to the owner): a PHP `send.php` modelled on
crestudio.ee's (save each lead to a file + `mail()`, honeypot, rate limit), plus a big `tel:` button,
because phone calls are the main channel. If the hosting has no PHP, fall back to a form service (e.g. Formspree).
Confirm with the owner before building it. Real logo and photos are still needed from the owner.

## Deploy

No CI/CD. The owner uploads files by hand: from the phone with **Termux**, or from the PC with **FileZilla**.
When work is done, list exactly which files changed so the owner knows what to upload.
If a lead file is added later (e.g. `data/leads.json`), it must not be in git and must never be overwritten on upload.

## Commands

No build step, no dependencies. Plain HTML/CSS/JS.

```bash
# Local preview
python3 -m http.server 8098        # then open http://127.0.0.1:8098/

# Check: page loads and linked files exist
curl -s -o /dev/null -w "%{http_code}\n" 127.0.0.1:8098/
for f in style.css script.js logo.png; do curl -s -o /dev/null -w "%{http_code} $f\n" "127.0.0.1:8098/$f"; done
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
