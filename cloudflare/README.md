# Cloudflare deployment

The live site runs as static files on a Cloudflare Worker (static assets),
with the enquiry form handled by the same Worker. WordPress (the
theme in `wp-content/themes/rian-cullet`) is still where the site is
built and edited; `tools/export-static.py` turns it into the files in
`public/`.

```
wrangler.jsonc              Worker config (repository root)
cloudflare/
├── public/                 generated site (commit it; Cloudflare serves it)
├── worker.js               Worker entry: static files + /api/enquiry
├── functions/api/enquiry.js  enquiry form handler
└── src/                    copied into public/: _headers, _redirects, enquiry.js
```

## Deployment (Cloudflare Workers, connected to GitHub)

1. Make sure the changes are on `main` (merge the pull request).
2. Cloudflare dashboard → **Workers & Pages → Create → Import a repository**,
   pick this repository. Settings:
   - Root directory: **`/`** (the repository root)
   - Build command: *(leave empty)*
   - Deploy command: **`npx wrangler deploy`**
3. The Worker's name must match `"name"` in `wrangler.jsonc`
   (`riancullets-com`). If you named it differently, change that line,
   commit and push.
4. Deploy. You get a `*.workers.dev` URL. Check it.

## Make the enquiry form deliver

In the Worker → **Settings → Variables and Secrets** (for KV: create the
namespace under **Storage & Databases → KV**, then add its id to the
`kv_namespaces` line in `wrangler.jsonc`):

| Name | Type | Value |
|---|---|---|
| `ENQUIRIES` | KV binding, set in `wrangler.jsonc` | create a namespace, e.g. `rian-enquiries`, paste its id |
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
2. Worker → **Settings → Domains & Routes → Add → Custom domain**:
   `riancullets.com` and `www.riancullets.com`.
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
3. Commit and push to `main`. Cloudflare redeploys automatically.

After launch, submit `https://riancullets.com/sitemap.xml` in Google
Search Console.
