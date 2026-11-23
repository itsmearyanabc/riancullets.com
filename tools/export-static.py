#!/usr/bin/env python3
"""Export the WordPress site to static files for Cloudflare Pages.

WordPress (this theme) stays the source of truth. This script crawls a
running local copy and writes a static site to cloudflare/public/:

  - every page in the sitemap, plus 404.html
  - theme assets (bundled CSS/JS, fonts, images)
  - sitemap.xml, robots.txt, llms.txt
  - _headers, _redirects and enquiry.js from cloudflare/src/

URLs are rewritten so links and assets are root-relative (the site works
on a *.pages.dev preview) while canonical, Open Graph and structured-data
URLs are absolute on the production domain. The enquiry form is pointed at
the Cloudflare Pages Function in cloudflare/functions/api/enquiry.js.

Usage (WordPress running locally, see README "Running it locally"):

    python3 tools/export-static.py
    python3 tools/export-static.py --base http://127.0.0.1:9400 --site https://riancullets.com
"""
import argparse
import pathlib
import re
import shutil
import sys
import urllib.error
import urllib.request
from xml.etree import ElementTree

ROOT = pathlib.Path(__file__).resolve().parent.parent
THEME = ROOT / "wp-content/themes/rian-cullet"
CF = ROOT / "cloudflare"
OUT = CF / "public"
THEME_URL_PATH = "/wp-content/themes/rian-cullet"


def fetch(url: str) -> tuple[int, str]:
    req = urllib.request.Request(url, headers={"User-Agent": "rc-static-export"})
    try:
        with urllib.request.urlopen(req, timeout=60) as res:
            return res.status, res.read().decode("utf-8")
    except urllib.error.HTTPError as err:
        return err.code, err.read().decode("utf-8")


def sitemap_urls(base: str) -> list[tuple[str, str]]:
    ns = {"s": "http://www.sitemaps.org/schemas/sitemap/0.9"}
    _, index = fetch(base + "/wp-sitemap.xml")
    out = []
    for loc in ElementTree.fromstring(index).findall("s:sitemap/s:loc", ns):
        _, xml = fetch(loc.text)
        for url in ElementTree.fromstring(xml).findall("s:url", ns):
            lastmod = url.find("s:lastmod", ns)
            out.append((url.find("s:loc", ns).text, lastmod.text if lastmod is not None else ""))
    return out


def transform(html: str, base: str, site: str) -> str:
    esc_base = base.replace("/", "\\/")
    esc_site = site.replace("/", "\\/")

    # WordPress head output that has no meaning on a static host.
    html = re.sub(r"<link rel=['\"]dns-prefetch['\"][^>]*>\s*", "", html)
    html = re.sub(r"<link rel=\"alternate\" type=\"application/rss\+xml\"[^>]*>\s*", "", html)
    html = re.sub(r"<link rel=['\"](EditURI|https://api\.w\.org/|shortlink)['\"][^>]*>\s*", "", html)
    html = re.sub(r"<link rel=\"alternate\"[^>]*(json|oembed)[^>]*>\s*", "", html)
    html = re.sub(r"<script type=\"speculationrules\">.*?</script>\s*", "", html, flags=re.S)

    # Enquiry form -> Cloudflare Pages Function.
    if "rc-form" in html:
        html = re.sub(r'(<form\s+class="rc-form"[^>]*?action=")[^"]*(")', r"\1/api/enquiry\2", html)
        html = re.sub(r'<input type="hidden" (?:id="rc_nonce" )?name="(action|rc_redirect|rc_nonce|_wp_http_referer)"[^>]*>\s*', "", html)
        html = re.sub(r'(<input type="hidden" name="rc_time" value=")[^"]*(")', r"\1\2", html)
        html = html.replace("</body>", '<script src="/enquiry.js" defer></script>\n</body>')

    # Tag by tag: navigation and asset URLs become root-relative; canonical
    # and alternate links stay absolute on the production domain.
    def fix_tag(m: re.Match) -> str:
        tag = m.group(0)
        if base not in tag:
            return tag
        if tag.startswith("<link") and re.search(r"rel=['\"](canonical|alternate)['\"]", tag):
            return tag.replace(base, site)
        def rel(a: re.Match) -> str:
            value = a.group(3).replace(base + "/", "/").replace(base, "/")
            return f"{a.group(1)}={a.group(2)}{value}{a.group(2)}"
        return re.sub(r"\b(href|src|srcset|imagesrcset|action)=(['\"])(.*?)\2", rel, tag)

    html = re.sub(r"<(?:a|link|img|source|script|form)\b[^>]*>", fix_tag, html)

    # Everything left (meta content, JSON-LD) is absolute on production.
    html = html.replace(base, site).replace(esc_base, esc_site)
    html = html.replace("127.0.0.1%3A9400", site.split("//", 1)[1]).replace("http%3A%2F%2F", "https%3A%2F%2F")
    return html


def copy_assets() -> None:
    src = THEME / "assets"
    dst = OUT / THEME_URL_PATH.lstrip("/") / "assets"
    for sub in ("dist", "fonts", "img"):
        shutil.copytree(src / sub, dst / sub, ignore=shutil.ignore_patterns("*.md", ".DS_Store"))
    for item in (CF / "src").iterdir():
        shutil.copy2(item, OUT / item.name)


def main() -> None:
    ap = argparse.ArgumentParser()
    ap.add_argument("--base", default="http://127.0.0.1:9400")
    ap.add_argument("--site", default="https://riancullets.com")
    args = ap.parse_args()
    base, site = args.base.rstrip("/"), args.site.rstrip("/")

    status, _ = fetch(base + "/")
    if status != 200:
        sys.exit(f"WordPress is not answering at {base} (HTTP {status}). Start it first.")

    if OUT.exists():
        shutil.rmtree(OUT)
    OUT.mkdir(parents=True)

    pages = sitemap_urls(base)
    if not pages:
        sys.exit("The sitemap is empty: have the pages been created?")

    # Linked from every page but kept out of the sitemap while noindex.
    extra = [(base + "/privacy-policy/", "")]
    for url, _ in pages + extra:
        status, html = fetch(url)
        if status != 200:
            sys.exit(f"{url} returned HTTP {status}")
        if re.search(r"<b>(Warning|Notice|Fatal error|Deprecated)</b>", html):
            sys.exit(f"{url} contains a PHP error; fix it before exporting")
        path = url[len(base):].strip("/")
        target = OUT / path / "index.html" if path else OUT / "index.html"
        target.parent.mkdir(parents=True, exist_ok=True)
        target.write_text(transform(html, base, site))
        print("page ", "/" + path)

    _, html = fetch(base + "/rc-static-export-404/")
    (OUT / "404.html").write_text(transform(html, base, site))
    print("page  404.html")

    _, llms = fetch(base + "/llms.txt")
    (OUT / "llms.txt").write_text(llms.replace(base, site))

    entries = "".join(
        f"<url><loc>{u.replace(base, site)}</loc>" + (f"<lastmod>{m}</lastmod>" if m else "") + "</url>"
        for u, m in pages
    )
    (OUT / "sitemap.xml").write_text(
        '<?xml version="1.0" encoding="UTF-8"?>\n'
        f'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">{entries}</urlset>\n'
    )
    (OUT / "robots.txt").write_text(
        f"User-agent: *\nDisallow: /api/\n\nSitemap: {site}/sitemap.xml\n"
    )

    copy_assets()

    leaks = [p for p in OUT.rglob("*.html") if base in p.read_text() or "127.0.0.1" in p.read_text()]
    if leaks:
        sys.exit(f"Local URLs left in: {[str(p) for p in leaks]}")

    print(f"\n{len(pages)} pages exported to {OUT}")


if __name__ == "__main__":
    main()
