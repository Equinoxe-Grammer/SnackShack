#!/usr/bin/env python3
"""
Simple Markdown link and anchor checker for this repo's docs.
Checks:
 - Markdown links [text](target)
 - Internal anchors like [text](#anchor)
 - Links to other .md files with optional anchors (other.md#anchor)

Usage: python tools/check_docs_toc.py
Exits with code 0 if no problems, 1 otherwise.
"""
import os
import re
import sys
from collections import defaultdict

ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))

md_paths = []
for dirpath, dirnames, filenames in os.walk(ROOT):
    # skip vendor and .git
    if 'vendor' in dirpath.split(os.sep):
        continue
    for fn in filenames:
        if fn.lower().endswith('.md'):
            md_paths.append(os.path.join(dirpath, fn))

header_re = re.compile(r'^(#{1,6})\s*(.*?)\s*$')
inline_anchor_re = re.compile(r'\{#([A-Za-z0-9_\-]+)\}\s*$')
html_anchor_re = re.compile(r'<a\s+(?:name|id)=["\']([^"\']+)["\']', re.IGNORECASE)
link_re = re.compile(r'\[([^\]]+)\]\(([^)]+)\)')

# compute anchors for each file
file_anchors = defaultdict(set)
file_headers = defaultdict(list)

def slugify(text):
    t = text.strip().lower()
    # remove code spans
    t = re.sub(r'`(.+?)`', r"\1", t)
    # remove html tags
    t = re.sub(r'<[^>]+>', '', t)
    # remove punctuation except spaces, hyphens and underscores
    t = re.sub(r'[^\w\s-]', '', t)
    t = re.sub(r'\s+', '-', t)
    t = t.strip('-')
    return t

for path in md_paths:
    anchors = set()
    headers = []
    try:
        with open(path, 'r', encoding='utf-8') as f:
            lines = f.readlines()
    except Exception:
        with open(path, 'r', encoding='latin-1') as f:
            lines = f.readlines()
    for i, line in enumerate(lines):
        m = header_re.match(line)
        if m:
            header_text = m.group(2)
            # check for explicit {#id}
            ma = inline_anchor_re.search(header_text)
            if ma:
                aid = ma.group(1)
                # remove the {#id} from header_text for storing header
                header_text = inline_anchor_re.sub('', header_text).strip()
                anchors.add(aid)
                headers.append((i+1, header_text, aid))
            else:
                # default slug
                s = slugify(header_text)
                if s:
                    anchors.add(s)
                headers.append((i+1, header_text, s))
        # find html anchors
        for ham in html_anchor_re.finditer(line):
            anchors.add(ham.group(1))
    file_anchors[path] = anchors
    file_headers[path] = headers

# now validate links
problems = []

for src in md_paths:
    try:
        with open(src, 'r', encoding='utf-8') as f:
            text = f.read()
    except Exception:
        with open(src, 'r', encoding='latin-1') as f:
            text = f.read()
    for lm in link_re.finditer(text):
        link_text = lm.group(1)
        target_raw = lm.group(2).strip()
        # skip mailto and external links
        if target_raw.startswith('http://') or target_raw.startswith('https://') or target_raw.startswith('mailto:'):
            continue
        # skip images with ![](...)
        # but regex didn't capture leading !, so okay
        # handle anchors-only
        if target_raw.startswith('#'):
            anchor = target_raw[1:]
            if anchor == '':
                continue
            if anchor not in file_anchors[src]:
                problems.append((src, None, link_text, target_raw, 'Missing anchor in same file'))
            continue
        # split into path and optional anchor
        if '#' in target_raw:
            path_part, anchor_part = target_raw.split('#', 1)
            anchor_part = anchor_part.strip()
        else:
            path_part, anchor_part = target_raw, None
        # resolve path_part relative to src
        # sometimes links are to 'docs/FOO.md' or './FOO.md' or '../FOO.md' or 'FOO.md'
        path_part = path_part.strip()
        if path_part == '':
            # link like (#anchor) handled before; fallback
            continue
        # normalize by removing any URL-encoded fragments or leading ./
        # ignore query strings
        path_part = path_part.split('?')[0].split('#')[0]
        # if path has anchor-style reference without .md, some use 'README.md' or 'INDEX.md'
        candidate = os.path.normpath(os.path.join(os.path.dirname(src), path_part))
        # if path has no extension, assume .md
        if not os.path.splitext(candidate)[1]:
            candidate_md = candidate + '.md'
            if os.path.exists(candidate_md):
                candidate = candidate_md
        # if candidate points to a directory, try README.md inside
        if os.path.isdir(candidate):
            candidate_index = os.path.join(candidate, 'README.md')
            if os.path.exists(candidate_index):
                candidate = candidate_index
        if not os.path.exists(candidate):
            # maybe target was absolute relative to repo root
            alt = os.path.normpath(os.path.join(ROOT, path_part.lstrip('/\\')))
            if os.path.exists(alt):
                candidate = alt
            else:
                problems.append((src, candidate, link_text, target_raw, 'Target file not found'))
                continue
        # now check anchor if present
        if anchor_part:
            # anchors in anchors set are already slugified in the same way
            # compute slug for anchor_part in case it's a header text rather than slug
            anchor_slug = anchor_part.strip()
            # if anchor looks like a header (contains spaces), slugify
            if ' ' in anchor_slug:
                anchor_slug = slugify(anchor_slug)
            if anchor_slug not in file_anchors[candidate]:
                problems.append((src, candidate, link_text, target_raw, f'Missing anchor "{anchor_part}" in target'))

# print summary
if not problems:
    print('OK: No broken Markdown file links or missing anchors found among', len(md_paths), 'files.')
    sys.exit(0)

print('Found', len(problems), 'problems across', len(md_paths), 'Markdown files. Details:')
for p in problems:
    src, target, text, raw, reason = p
    print('\n- Source:', os.path.relpath(src, ROOT))
    if target:
        print('  -> Target:', os.path.relpath(target, ROOT))
    else:
        print('  -> Target: (same file)')
    print('  -> Link text:', text)
    print('  -> Raw href:', raw)
    print('  -> Problem:', reason)

# optional: suggest fixes for missing anchors that are simple
print('\nSuggestions:')
for p in problems:
    src, target, text, raw, reason = p
    if reason.startswith('Missing anchor') and target:
        # if anchor missing, suggest header slug or adding anchor
        if '#' in raw:
            path_part, anchor_part = raw.split('#', 1)
            cand_slug = anchor_part.strip()
            if ' ' in cand_slug:
                cand_slug = slugify(cand_slug)
            print(f'- In {os.path.relpath(src, ROOT)} link "{text}" points to {os.path.relpath(target, ROOT)}#{anchor_part}.')
            print('  Suggest: change the link anchor to #'+cand_slug+" or add a header/id '"+cand_slug+"' in the target file.")
    elif reason == 'Target file not found':
        print(f'- In {os.path.relpath(src, ROOT)} link "{text}" points to missing file {raw}. Please verify the path or create the target.')
    elif reason == 'Missing anchor in same file':
        print(f'- In {os.path.relpath(src, ROOT)} link "{text}" points to {raw} but that anchor is missing; consider adding the header or using the correct slug.')

sys.exit(1)
