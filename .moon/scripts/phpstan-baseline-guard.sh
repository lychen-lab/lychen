#!/usr/bin/env bash

# Fails when a pull request adds errors to a PHPStan baseline. The baseline is
# frozen debt: it may shrink, never grow. Regenerating it to absorb new errors
# would hide them.
#
# Usage: phpstan-baseline-guard.sh <baseline path from the repository root>

set -euo pipefail

baseline="$1"
base="${GITHUB_BASE_REF:-}"

cd "$(git rev-parse --show-toplevel)"

if [[ -z "$base" ]]; then
  echo "Not a pull request, nothing to compare."
  exit 0
fi

total() { grep -E '^[[:space:]]+count: [0-9]+$' | awk '{ s += $2 } END { print s + 0 }'; }

if ! git cat-file -e "origin/$base:$baseline" 2> /dev/null; then
  echo "No baseline on $base yet, nothing to compare."
  exit 0
fi

before=$(git show "origin/$base:$baseline" | total)
after=$(total < "$baseline")
echo "Baselined errors: $before on $base, $after on this PR."
if (( after > before )); then
  echo "::error title=PHPStan baseline grew::$baseline went from $before to $after errors. Fix the new errors instead of baselining them."
  exit 1
fi
