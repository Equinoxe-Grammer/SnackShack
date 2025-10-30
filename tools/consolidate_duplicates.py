#!/usr/bin/env python3
"""
Conservative consolidator:
- Reads tools/duplicate_paragraphs.json produced by find_duplicate_md.py
- For each paragraph that appears in >=2 files and length >= 120 chars,
  create an entry in docs/COMMON_SNIPPETS.md with a generated header.
- Replace the paragraph occurrences with a short reference line pointing to the snippet.
- Backups: create .bak files for edited files.

Be careful: this is conservative and only replaces exact paragraph matches.
"""
import os
import json
import hashlib
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
DUP_PATH = ROOT / 'tools' / 'duplicate_paragraphs.json'
COMMON = ROOT / 'docs' / 'COMMON_SNIPPETS.md'

if not DUP_PATH.exists():
    print('MISSING', DUP_PATH)
    raise SystemExit(1)

with open(DUP_PATH, 'r', encoding='utf-8') as fh:
    duplicates = json.load(fh)

# filter candidates
candidates = {p: paths for p, paths in duplicates.items() if len(set(paths)) >= 2 and len(p) >= 120}

if not candidates:
    print('NO_CONSOLIDATION_CANDIDATES')
    raise SystemExit(0)

print(f'Candidates: {len(candidates)}')

# ensure docs exists
(COMMON.parent).mkdir(parents=True, exist_ok=True)

# load existing common file
if COMMON.exists():
    with open(COMMON, 'r', encoding='utf-8') as fh:
        common_text = fh.read()
else:
    common_text = '# Common snippets and canonical paragraphs\n\nThis file collects canonical paragraphs used across the documentation.\n\n'

added = []
replacements = []

for p, paths in candidates.items():
    # generate short title from first words
    snip = p.strip()
    title_words = re.sub(r"\s+", ' ', snip).split()[:8]
    title = ' '.join(title_words).rstrip('.,;:')
    slug = re.sub(r'[^a-z0-9\-]+', '-', title.lower())
    # ensure unique slug
    base = slug
    i = 1
    while re.search(rf"^##\s+.*\b{slug}\b", common_text, re.I):
        i += 1
        slug = f"{base}-{i}"
    header = f"## {title} <a id=\"{slug}\"></a>\n\n"
    block = header + snip + '\n\n'
    common_text += block
    added.append((title, slug))

    # replacement pattern: exact paragraph match, allow whitespace normalization
    norm = re.sub(r"\s+", ' ', snip).strip()
    repl_line = f"> Nota: esta sección consolidada se encuentra en [COMMON_SNIPPETS.md](COMMON_SNIPPETS.md) — {title} (#${slug})\n"

    for relpath in set(paths):
        fpath = ROOT / relpath
        if not fpath.exists():
            print('MISSING FILE', fpath)
            continue
        with open(fpath, 'r', encoding='utf-8') as fh:
            content = fh.read()
        # find the paragraph occurrence (loose match)
        # split by blank lines
        parts = re.split(r'(\n\s*\n)', content)
        joined = ''.join(parts)
        # try to replace the exact paragraph (normalized whitespace)
        replaced = False
        def norm_s(s):
            return re.sub(r"\s+", ' ', s).strip()
        # find candidate paragraphs
        paras = re.split(r'\n\s*\n', content)
        new_paras = []
        did = False
        for para in paras:
            if norm_s(para) == norm and not did:
                new_paras.append(repl_line)
                did = True
            else:
                new_paras.append(para)
        if did:
            # backup
            bak = fpath.with_suffix(fpath.suffix + '.bak')
            if not bak.exists():
                with open(bak, 'w', encoding='utf-8') as fh:
                    fh.write(content)
            new_content = '\n\n'.join(new_paras)
            with open(fpath, 'w', encoding='utf-8') as fh:
                fh.write(new_content)
            replacements.append(str(fpath.relative_to(ROOT)))
            print('Replaced in', fpath.relative_to(ROOT))

# write COMMON file
with open(COMMON, 'w', encoding='utf-8') as fh:
    fh.write(common_text)

print('\nWROTE', COMMON.relative_to(ROOT))
print('ADDED', len(added), 'snippets')
print('REPLACED in', len(set(replacements)), 'files')
