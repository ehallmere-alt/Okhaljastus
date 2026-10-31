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

Ask the owner before choosing how the form should deliver leads (mailto, a PHP `send.php` like on
crestudio.ee, or a form service), and for the real logo and photos.

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
