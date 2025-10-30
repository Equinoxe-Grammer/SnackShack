#!/usr/bin/env python3
"""
Add explicit HTML anchors before each Markdown heading to ensure links
using different slug variants resolve. Inserts two anchors per heading:
  <a id="slug"></a>
  <a id="-slug"></a>

This is conservative and non-destructive (anchors inserted above headings).
Run from repo root:
python tools/add_heading_anchors.py
"""
import re
import sys
import unicodedata
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
DOCS_DIR = ROOT / "docs"

MD_FILES = list(DOCS_DIR.rglob("*.md"))
README = ROOT / "README.md"
if README.exists():
    MD_FILES.insert(0, README)

HEADING_RE = re.compile(r'^(#{1,6})\s*(.+)$', flags=re.MULTILINE)


def slugify(text: str) -> str:
    # Remove accents
    text = unicodedata.normalize('NFKD', text)
    text = ''.join(ch for ch in text if not unicodedata.combining(ch))
    s = text.strip().lower()
    # remove leading non-alphanumeric (like emoji) so we don't create leading hyphens
    s = re.sub(r'^[^a-z0-9]+', '', s)
    s = re.sub(r"[\s]+", "-", s)
    s = re.sub(r"[^a-z0-9\-\_]", "", s)
    s = re.sub(r"-+", "-", s)
    return s


def process(path: Path):
    original = path.read_text(encoding='utf-8')
    content = original
    changed = False

    # walk through headings and insert anchors before them if missing
    offset = 0
    for m in list(HEADING_RE.finditer(content)):
        start = m.start()
        # Adjust start by offset if we've changed content earlier
        # We'll rebuild content in a simple way: splitlines and process
    
    lines = content.splitlines(keepends=True)
    new_lines = []
    i = 0
    while i < len(lines):
        line = lines[i]
        h = re.match(r'^(#{1,6})\s*(.+)', line)
        if h:
            level = len(h.group(1))
            title = h.group(2).strip()
            # compute slug
            slug = slugify(title)
            if not slug:
                # fallback to sanitized filename-based slug
                slug = path.stem.lower().replace(' ', '-')
            anchor1 = f'<a id="{slug}"></a>\n'
            anchor2 = f'<a id="-{slug}"></a>\n'
            # check previous lines to avoid duplicate insertion
            prev = ''.join(new_lines[-2:]) if len(new_lines) >= 2 else ''.join(new_lines)
            if anchor1.strip() in prev or (anchor2.strip() in prev):
                # already present, keep heading
                new_lines.append(line)
                i += 1
                continue
            # insert anchors then the heading
            new_lines.append(anchor1)
            # also insert the hyphen variant to be tolerant of older links
            new_lines.append(anchor2)
            new_lines.append(line)
            changed = True
            i += 1
        else:
            new_lines.append(line)
            i += 1

    new_content = ''.join(new_lines)
    # Normalize multiple blank lines
    new_content = re.sub(r'\n{3,}', '\n\n', new_content)
    if changed and new_content != original:
        path.write_text(new_content, encoding='utf-8')
        return True
    return False


def main():
    changed_files = []
    for p in MD_FILES:
        if not p.exists():
            continue
        try:
            if process(p):
                changed_files.append(str(p.relative_to(ROOT)))
        except Exception as e:
            print(f'Error processing {p}: {e}', file=sys.stderr)
    print('\nAnchor injection complete.')
    if changed_files:
        print('Files changed:')
        for f in changed_files:
            print(' -', f)
    else:
        print('No files required anchor insertion.')

if __name__ == '__main__':
    main()
