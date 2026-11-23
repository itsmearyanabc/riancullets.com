# Cloudflare Pages deployment

The live site runs as static files on Cloudflare Pages. WordPress (the
theme in `wp-content/themes/rian-cullet`) is still where the site is
built and edited; `tools/export-static.py` turns it into the files in
`public/`.

```
cloudflare/
├── public/                 generated site (commit it; Cloudflare serves it)
├── functions/api/enquiry.js  enquiry form handler (POST /api/enquiry)
├── src/                    copied into public/: _headers, _redirects, enquiry.js
└── wrangler.toml
```

## First deployment (Git integration, recommended)

Cloudflare's drag-and-drop upload does **not** run Functions, so the
enquiry form needs Git or Wrangler.

1. Push this repository to GitHub (`public/` included).
2. Cloudflare dashboard → **Workers & Pages → Create → Pages → Connect to Git**,
   pick the repository.
3. Build settings:
   - Framework preset: **None**
   - Build command: *(leave empty)*
   - Build output directory: **`public`**
   - Root directory (advanced): **`cloudflare`**
4. **Save and Deploy.** You get a `*.pages.dev` preview URL. Check it.

Alternative without Git: `npx wrangler pages deploy` from the
`cloudflare/` folder (this also uploads `functions/`).

## Make the enquiry form deliver

In the Pages project → **Settings → Variables and Secrets / Bindings**:

| Name | Type | Value |
|---|---|---|
| `ENQUIRIES` | KV namespace binding | create a namespace, e.g. `rian-enquiries` |
| `RESEND_API_KEY` | Secret | API key from resend.com (free tier is enough) |
| `ENQUIRY_TO` | Variable | inbox for enquiries; several allowed, comma separated |
| `ENQUIRY_FROM` | Variable | e.g. `Rian Cullet <enquiries@riancullets.com>` (domain verified in Resend) |

At least **one** of KV or email must be set, otherwise the form tells
visitors it is unavailable instead of losing their message. Set both:
KV keeps every enquiry even if email fails (view them under
**Workers & Pages → KV → rian-enquiries**). Redeploy after changing
settings.

## Point riancullets.com at it

1. Add `riancullets.com` to Cloudflare (if not already) and switch the
   domain's nameservers to Cloudflare's at the registrar.
   **Before switching, copy any email (MX/TXT) records** from Hostinger so
   mail keeps working.
2. Pages project → **Custom domains** → add `riancullets.com` and
   `www.riancullets.com`.
3. Redirect www to the bare domain: **Rules → Redirect Rules →**
   `www.riancullets.com/*` → `https://riancullets.com/${1}` (301).
4. SSL/TLS → **Full (strict)**; turn **Rocket Loader off**.

Old WordPress URLs (`/wp-sitemap.xml`, `/hello-world/`, `/feed/`…) are
redirected by `src/_redirects`.

## Updating the site

1. Edit the theme, then run WordPress locally (see the main README:
   `npx @wp-playground/cli … server`, http://127.0.0.1:9400).
2. From the repository root:

   ```bash
   python3 tools/build-assets.py
   python3 tools/export-static.py
   ```

   The export stops with an error if any page fails, shows a PHP error
   or still contains a local URL.
3. Commit and push. Cloudflare redeploys automatically.

After launch, submit `https://riancullets.com/sitemap.xml` in Google
Search Console.
