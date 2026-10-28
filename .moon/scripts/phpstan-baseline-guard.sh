#!/usr/bin/env bash

# Fails when a branch adds errors to a PHPStan baseline. The baseline is frozen
# debt: it may shrink, never grow. Regenerating it to absorb new errors would
# hide them.
#
# The branch is compared with the commit it forked from: the pull request's
# base branch in CI, main elsewhere. Comparing with the tip of that branch
# instead would count the errors fixed there since as errors added here.
#
# Usage: phpstan-baseline-guard.sh <baseline path from the repository root>

set -euo pipefail

baseline="$1"
base="origin/${GITHUB_BASE_REF:-main}"

cd "$(git rev-parse --show-toplevel)"

total() { grep -E '^[[:space:]]+count: [0-9]+$' | awk '{ s += $2 } END { print s + 0 }'; }

if ! fork=$(git merge-base HEAD "$base" 2> /dev/null); then
  echo "No common history with $base, nothing to compare."
  exit 0
fi

if ! git cat-file -e "$fork:$baseline" 2> /dev/null; then
  echo "No baseline on $base yet, nothing to compare."
  exit 0
fi

before=$(git show "$fork:$baseline" | total)
after=$(total < "$baseline")
echo "Baselined errors: $before on $base, $after on this branch."
if (( after > before )); then
  echo "::error title=PHPStan baseline grew::$baseline went from $before to $after errors. Fix the new errors instead of baselining them."
  exit 1
fi
