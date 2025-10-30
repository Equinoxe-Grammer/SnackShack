#!/usr/bin/env python3
"""
Safe Markdown formatter for repository docs.
Behaviors (non-destructive, idempotent):
 - Normalize line endings to LF
 - Trim trailing whitespace
 - Ensure one blank line after any heading line (#..)
 - Collapse runs of more than 2 blank lines to 2
 - Normalize list bullets to '-' (from '*' or '+') when safe
 - If a fenced code block has no language, try to infer (php, bash, json, yaml, powershell)

Run: python tools/format_markdown.py
"""
import os
import re

ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))

md_files = []
for dirpath, dirnames, filenames in os.walk(ROOT):
    if 'vendor' in dirpath.split(os.sep):
        continue
    for fn in filenames:
        if fn.lower().endswith('.md'):
            md_files.append(os.path.join(dirpath, fn))

heading_re = re.compile(r'^(#{1,6})(\s+.*)$')
list_re = re.compile(r'^([ \t]*)([*+]\s+)(.*)$')
code_fence_re = re.compile(r'^(\s*)```(.*)$')

changed_files = []

def infer_lang(sample_lines):
    s = '\n'.join(sample_lines).lstrip()
    if s.startswith('<?php') or re.search(r"\bnamespace\b|\bclass\b|\bfunction\b", s):
        return 'php'
    if s.startswith('{') or re.search(r'"[A-Za-z0-9_-]+"\s*:', s):
        return 'json'
    if re.search(r'^\s*-\s', s, re.MULTILINE) or re.search(r':\s', s):
        # could be yaml but be conservative
        return 'yaml'
    if re.search(r'^\s*sudo |^\s*apt |^\s*systemctl |php |composer |curl |git ', s, re.MULTILINE):
        return 'bash'
    if re.search(r'PS [>$]', s) or re.search(r'Get-Process|Set-Item', s):
        return 'powershell'
    return None

for path in md_files:
    try:
        with open(path, 'r', encoding='utf-8') as f:
            raw = f.read()
    except Exception:
        with open(path, 'r', encoding='latin-1') as f:
            raw = f.read()
    orig = raw
    # normalize line endings
    raw = raw.replace('\r\n', '\n').replace('\r', '\n')
    lines = raw.split('\n')
    out_lines = []
    i = 0
    in_code = False
    code_lang = None
    code_buffer = []
    while i < len(lines):
        line = lines[i]
        # trim trailing whitespace
        line = line.rstrip()
        # detect code fences
        m_fence = code_fence_re.match(line)
        if m_fence:
            fence_lang = m_fence.group(2).strip()
            if not in_code:
                # starting a code block
                in_code = True
                code_lang = fence_lang if fence_lang else None
                code_buffer = []
                # emit fence without language for now; may add later
                out_lines.append('```' + (fence_lang and fence_lang or ''))
                i += 1
                continue
            else:
                # ending code block
                in_code = False
                # if block had no language, try infer
                if (not code_lang) and code_buffer:
                    guess = infer_lang(code_buffer[:6])
                    if guess:
                        # replace previous fence (last occurrence in out_lines)
                        # find last '```' line and append language
                        for j in range(len(out_lines)-1, -1, -1):
                            if out_lines[j].startswith('```'):
                                if out_lines[j].strip() == '```':
                                    out_lines[j] = '```' + guess
                                break
                out_lines.append('```')
                i += 1
                continue
        if in_code:
            code_buffer.append(line)
            out_lines.append(line)
            i += 1
            continue
        # ensure one blank line after headings
        m = heading_re.match(line)
        out_lines.append(line)
        if m:
            # if next line exists and is not blank, insert a blank line
            if i+1 < len(lines) and lines[i+1].strip() != '':
                out_lines.append('')
        i += 1
    # collapse >2 blank lines
    text = '\n'.join(out_lines)
    text = re.sub(r'\n{3,}', '\n\n', text)
    # normalize list bullets: convert '* ' or '+ ' to '- ' when at start of list line
    def repl_list(m):
        return m.group(1) + '- ' + m.group(3)
    text = list_re.sub(repl_list, text)
    # ensure file ends with newline
    if not text.endswith('\n'):
        text = text + '\n'
    if text != orig:
        try:
            with open(path, 'w', encoding='utf-8') as f:
                f.write(text)
            changed_files.append(path)
        except Exception:
            with open(path, 'w', encoding='latin-1') as f:
                f.write(text)
            changed_files.append(path)

print('Formatter run complete. Files changed:', len(changed_files))
for p in changed_files:
    print('-', os.path.relpath(p, ROOT))

if changed_files:
    print('\nRun your link/anchor checker to validate links after formatting.')
