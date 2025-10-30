#!/usr/bin/env python3
"""
Fix broken internal links of the form [text](#anchor) inside the same Markdown file.
Strategy:
 - Build a set of anchors present in the file (from explicit <a id="..."></a> and from headings via slugify).
 - For each internal link, if the target anchor is not present, try to find the closest anchor using difflib.get_close_matches.
 - Replace href with the best match when confidence is high.

Run: python tools/fix_internal_links.py
"""
import re
import sys
import unicodedata
from pathlib import Path
from difflib import get_close_matches

ROOT = Path(__file__).resolve().parent.parent
DOCS_DIR = ROOT / 'docs'
MD_FILES = list(DOCS_DIR.rglob('*.md'))
README = ROOT / 'README.md'
if README.exists():
    MD_FILES.insert(0, README)

HEADING_RE = re.compile(r'^(#{1,6})\s*(.+)$', flags=re.MULTILINE)
HTML_ANCHOR_RE = re.compile(r'<a\s+id="([^"]+)"></a>')
MD_LINK_RE = re.compile(r'\[([^\]]+)\]\(\s*(#[-A-Za-z0-9_]+)\s*\)')


def slugify(text: str) -> str:
    text = unicodedata.normalize('NFKD', text)
    text = ''.join(ch for ch in text if not unicodedata.combining(ch))
    s = text.strip().lower()
    s = re.sub(r'^[^a-z0-9]+', '', s)
    s = re.sub(r'\s+', '-', s)
    s = re.sub(r'[^a-z0-9\-_]', '', s)
    s = re.sub(r'-+', '-', s)
    return s


def collect_anchors(text: str):
    anchors = set()
    # from explicit HTML anchors
    for m in HTML_ANCHOR_RE.finditer(text):
        anchors.add(m.group(1))
    # from headings
    for m in HEADING_RE.finditer(text):
        title = m.group(2).strip()
        s = slugify(title)
        if s:
            anchors.add(s)
            anchors.add('-' + s)
    return anchors


def fix_file(path: Path):
    original = path.read_text(encoding='utf-8')
    anchors = collect_anchors(original)
    if not anchors:
        return False
    changed = False

    def repl(m):
        nonlocal changed
        text = m.group(1)
        raw = m.group(2)  # includes leading #
        anchor = raw.lstrip('#')
        if anchor in anchors:
            return m.group(0)
        # try to find close match
        candidates = list(anchors)
        # get best matches
        matches = get_close_matches(anchor, candidates, n=1, cutoff=0.6)
        if matches:
            best = matches[0]
            changed = True
            return f'[{text}](#{best})'
        # also try normalized form
        norm = slugify(anchor)
        if norm in anchors:
            changed = True
            return f'[{text}](#{norm})'
        # try with/without leading hyphen
        if anchor.startswith('-') and anchor[1:] in anchors:
            changed = True
            return f'[{text}](#{anchor[1:]})'
        if ('-' + anchor) in anchors:
            changed = True
            return f'[{text}](#-{anchor})'
        return m.group(0)

    new_text = MD_LINK_RE.sub(repl, original)
    if changed and new_text != original:
        path.write_text(new_text, encoding='utf-8')
        return True
    return False


def main():
    changed_files = []
    for p in MD_FILES:
        if not p.exists():
            continue
        try:
            if fix_file(p):
                changed_files.append(str(p.relative_to(ROOT)))
        except Exception as e:
            print(f'Error fixing {p}: {e}', file=sys.stderr)
    print('\nInternal link fixing complete.')
    if changed_files:
        print('Files changed:')
        for f in changed_files:
            print(' -', f)
    else:
        print('No internal links required fixing.')

if __name__ == '__main__':
    main()
