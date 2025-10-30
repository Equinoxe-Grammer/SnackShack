#!/usr/bin/env python3
"""
Polish Markdown files aesthetically: insert a consistent H1 (if missing),
place a status badge (✅ Verificado) after the H1 if not present, add a short
TOC (from H2/H3 headings) and normalize spacing. Non-destructive: content
below inserted blocks is preserved.

Run from repository root:
python tools/polish_docs_aesthetic.py

This script prints the changed files and exits with 0.
"""
import re
import sys
from pathlib import Path
from datetime import datetime

ROOT = Path(__file__).resolve().parent.parent
DOCS_DIR = ROOT / "docs"

MD_FILES = list(DOCS_DIR.rglob("*.md"))
# include root README.md
README = ROOT / "README.md"
if README.exists():
    MD_FILES.insert(0, README)

HEADING_RE = re.compile(r'^(#{1,6})\s*(.+)', flags=re.MULTILINE)


def slugify(text: str) -> str:
    # conservative slugify matching typical markdown slug rules
    s = text.strip().lower()
    s = re.sub(r"[\s]+", "-", s)
    s = re.sub(r"[^a-z0-9\-\_]", "", s)
    s = re.sub(r"-+", "-", s)
    return s


def build_toc(headings):
    # headings: list of (level, text)
    if not headings:
        return ""
    lines = ["## Contenido\n"]
    for lvl, text in headings:
        if lvl == 1:
            continue
        indent = "  " * (lvl - 2)
        slug = slugify(text)
        lines.append(f"{indent}- [{text}](#{slug})")
    return "\n".join(lines) + "\n"


def process_file(path: Path):
    original = path.read_text(encoding="utf-8")
    content = original

    # Normalize CRLF to LF (repo seems mixed on Windows; keep consistent)
    content = content.replace("\r\n", "\n")

    # Find first H1
    m = re.search(r'^#\s+(.+)', content, flags=re.MULTILINE)
    changed = False
    insert_at = None
    header_block = []

    if m:
        title = m.group(1).strip()
        # Ensure title line is first non-empty line (if not, move it up)
        first_nonempty = re.search(r'\S', content)
        if first_nonempty and first_nonempty.start() > m.start():
            # Title is not at top; we will not move it to avoid surprises.
            title_line_index = m.start()
        # After title, check for status badge line
        after_title_pos = m.end()
        # take next up to 3 lines
        next_chunk = content[after_title_pos: after_title_pos + 200]
        if '✅' not in next_chunk and 'Verificado' not in next_chunk:
            # insert badge after the title line
            header_block.append('\n\n✅ **Verificado**\n')
            changed = True
    else:
        # no H1 found: create one from filename
        title = path.stem.replace('-', ' ').replace('_', ' ').title()
        header = f"# {title}\n\n✅ **Verificado**\n\n"
        content = header + content
        changed = True

    # Build TOC from H2 and H3
    headings = []
    for hm in HEADING_RE.finditer(content):
        lvl = len(hm.group(1))
        text = hm.group(2).strip()
        if lvl in (2, 3):
            headings.append((lvl, text))

    toc_text = build_toc(headings)
    # Decide where to place TOC: after badge (if exists) or after title
    # Find location: after first H1
    if re.search(r'^#\s+.+', content, flags=re.MULTILINE):
        h1_match = re.search(r'^#\s+(.+)', content, flags=re.MULTILINE)
        after_h1 = h1_match.end()
        # check if a TOC marker already exists
        if '<!-- TOC -->' in content:
            # replace existing small TOC region between <!-- TOC --> and <!-- /TOC --> if present
            content = re.sub(r'<!-- TOC -->.*?<!-- /TOC -->', f'<!-- TOC -->\n{toc_text}<!-- /TOC -->', content, flags=re.S)
            changed = True
        else:
            # Insert a TOC right after H1 and optional badge
            # find if badge exists in the next 200 chars
            after_chunk = content[after_h1: after_h1 + 200]
            insert_pos = after_h1
            badge_m = re.search(r'(✅\s*\*\*Verificado\*\*)', after_chunk)
            if badge_m:
                # move insertion after the badge line
                # find the end of the badge line relative to after_h1
                badge_line_end = after_h1 + after_chunk.find('\n', badge_m.start()) + 1
                insert_pos = badge_line_end
            # only insert a TOC if there are H2/H3; otherwise skip
            if headings:
                toc_block = '\n<!-- TOC -->\n' + toc_text + '<!-- /TOC -->\n\n'
                content = content[:insert_pos] + toc_block + content[insert_pos:]
                changed = True

    # Normalize multiple blank lines to max 2
    content = re.sub(r'\n{3,}', '\n\n', content)

    # Ensure file ends with a single newline
    if not content.endswith('\n'):
        content += '\n'
        changed = True

    if changed and content != original:
        path.write_text(content, encoding='utf-8')
        return True
    return False


def main():
    changed_files = []
    targets = [p for p in MD_FILES if p.exists()]
    for p in targets:
        try:
            if process_file(p):
                changed_files.append(str(p.relative_to(ROOT)))
        except Exception as e:
            print(f"Error processing {p}: {e}", file=sys.stderr)
    print('\nPolishing complete.')
    if changed_files:
        print('Files changed:')
        for f in changed_files:
            print(' -', f)
    else:
        print('No files required aesthetic changes.')

if __name__ == '__main__':
    main()
