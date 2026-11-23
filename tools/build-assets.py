#!/usr/bin/env python3
"""Bundle the theme's CSS and JS into assets/dist/.

The files in assets/css and assets/js stay the files you edit.
inc/enqueue.php loads assets/dist/site.min.css and site.min.js when both
exist (and SCRIPT_DEBUG is off), turning 12 requests into 2.

Re-run after editing any CSS or JS, before building the theme zip:

    python3 tools/build-assets.py

No dependencies. Minification is deliberately conservative: CSS loses
comments and redundant whitespace; JS loses only block comments and
whole-line comments, since the server compresses the rest anyway.
"""
import pathlib
import re

ROOT = pathlib.Path(__file__).resolve().parent.parent
ASSETS = ROOT / "wp-content/themes/rian-cullet/assets"
CSS = ["00-tokens", "01-reset", "02-typography", "03-layout",
       "04-components", "05-sections", "06-landing", "07-motion"]
JS = ["nav", "reveal", "hero", "counters", "process"]

STRING = r'"(?:\\.|[^"\\])*"|\'(?:\\.|[^\'\\])*\''


def minify_css(src: str) -> str:
    # Split into strings and non-strings so nothing inside quotes changes.
    parts = re.split(f"({STRING})", src)
    out = []
    for i, part in enumerate(parts):
        if i % 2:
            out.append(part)
            continue
        part = re.sub(r"/\*.*?\*/", "", part, flags=re.S)
        part = re.sub(r"\s+", " ", part)
        part = re.sub(r"\s*([{};,>])\s*", r"\1", part)
        part = part.replace(";}", "}")
        out.append(part)
    return "".join(out).strip() + "\n"


def strip_js(src: str) -> str:
    parts = re.split(f"({STRING})", src)
    out = []
    for i, part in enumerate(parts):
        if i % 2:
            out.append(part)
            continue
        part = re.sub(r"/\*.*?\*/", "", part, flags=re.S)
        part = re.sub(r"(?m)^\s*//.*$", "", part)
        out.append(part)
    text = "".join(out)
    text = re.sub(r"[ \t]+$", "", text, flags=re.M)
    text = re.sub(r"\n\s*\n+", "\n", text)
    return text.strip() + "\n"


def main() -> None:
    dist = ASSETS / "dist"
    dist.mkdir(exist_ok=True)

    css = "\n".join((ASSETS / "css" / f"{n}.css").read_text() for n in CSS)
    (dist / "site.min.css").write_text(minify_css(css))

    js = "\n;\n".join(strip_js((ASSETS / "js" / f"{n}.js").read_text()) for n in JS)
    (dist / "site.min.js").write_text(js)

    for f in ("site.min.css", "site.min.js"):
        print(f, (dist / f).stat().st_size, "bytes")


if __name__ == "__main__":
    main()
