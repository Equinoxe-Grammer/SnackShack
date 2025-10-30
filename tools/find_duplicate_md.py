#!/usr/bin/env python3
import os
import re
from collections import defaultdict

ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))

IGNORE_DIRS = {'vendor', '.git', 'node_modules'}

md_files = []
for dirpath, dirnames, filenames in os.walk(ROOT):
    parts = set(dirpath.split(os.sep))
    if parts & IGNORE_DIRS:
        continue
    for f in filenames:
        if f.lower().endswith('.md'):
            md_files.append(os.path.join(dirpath, f))

para_map = defaultdict(list)

def normalize_para(p):
    p = p.strip()
    # remove multiple whitespace
    p = re.sub(r"\s+", ' ', p)
    return p

for path in md_files:
    try:
        with open(path, 'r', encoding='utf-8') as fh:
            text = fh.read()
    except Exception as e:
        print(f"SKIP {path}: {e}")
        continue
    # remove code fences temporarily
    parts = re.split(r'```[\s\S]*?```', text)
    # split paragraphs by blank lines
    paras = re.split(r'\n\s*\n', text)
    for i, p in enumerate(paras):
        p2 = p.strip()
        if not p2:
            continue
        # skip headings and single-line TOC bullets
        if re.match(r'^(#{1,6}\s)|(^<a id=)', p2):
            continue
        # skip short lines
        norm = normalize_para(p2)
        if len(norm) < 40:
            continue
        para_map[norm].append(path)

# print duplicates
duplicates = {p: paths for p, paths in para_map.items() if len(set(paths)) > 1}

if not duplicates:
    print('NO_DUPLICATES')
else:
    print(f'DUPLICATES_FOUND: {len(duplicates)}')
    for i, (p, paths) in enumerate(sorted(duplicates.items(), key=lambda kv: -len(set(kv[1])))):
        print('\n--- DUP %d ---' % (i+1))
        print(p)
        for path in sorted(set(paths)):
            print('  -', os.path.relpath(path, ROOT))

# write a small JSON-like file with results for use by consolidate script
try:
    import json
    out = os.path.join(ROOT, 'tools', 'duplicate_paragraphs.json')
    serial = {p: sorted(list(set(paths))) for p, paths in duplicates.items()}
    with open(out, 'w', encoding='utf-8') as fh:
        json.dump(serial, fh, ensure_ascii=False, indent=2)
    print('\nWROTE', out)
except Exception as e:
    print('COULD_NOT_WRITE_JSON', e)
