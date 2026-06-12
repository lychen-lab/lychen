#!/usr/bin/env bash

set -euo pipefail

cd "$(git rev-parse --show-toplevel)"

# Capture the files that were staged before running the fixers, so that only
# those files are restaged afterwards. A blanket `git add -u` would silently
# stage unrelated changes the developer deliberately left unstaged.
stagedFiles=()
while IFS= read -r -d '' file; do
  stagedFiles+=("$file")
done < <(git diff --cached --name-only --diff-filter=ACMR -z)

echo '> Run fixers on affected files'
moon run :format-fix --affected
moon run :lint-fix --affected

if [ ${#stagedFiles[@]} -eq 0 ]; then
  echo '> No staged files to restage'
  exit 0
fi

echo '> Restage originally staged files'
for file in "${stagedFiles[@]}"; do
  # Skip files that no longer exist (e.g. deleted by a fixer)
  if [ -e "$file" ]; then
    git add -- "$file"
  fi
done
