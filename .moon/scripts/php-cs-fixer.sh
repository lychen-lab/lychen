#!/usr/bin/env bash
# php-cs-fixer for the whole monorepo, from its official image: it needs Docker,
# not PHP on the host, nor the API's container. The PHP projects' format and
# format-fix tasks (.moon/tasks/php.yml) and the editor (.vscode/settings.json)
# run it the same way, with the one config of the monorepo (/.php-cs-fixer.php).
# Takes the arguments of php-cs-fixer itself.
#
# The repository is mounted at its own path, so every path the caller passes
# (the config, a file, a project's root) means the same in the container. A
# path outside the repository, such as the temporary copy of the file that the
# VSCode extension formats on save, has its directory mounted the same way.
set -euo pipefail

# The one php-cs-fixer version of the monorepo: bumping it here bumps every
# PHP project. Keep the PHP version in step with the APIs' runtime (Dockerfile):
# php-cs-fixer parses with the PHP it runs on.
image='ghcr.io/php-cs-fixer/php-cs-fixer:3.95.27-php8.5'

root="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"

mounts=(--volume "$root:$root")
for arg in "$@"; do
  # Plain paths, and the value of options such as --config=<path>.
  path="${arg#--*=}"
  [[ "$path" == /* && -e "$path" ]] || continue
  case "$path/" in "$root"/*) continue ;; esac
  [[ -d "$path" ]] || path="$(dirname "$path")"
  mounts+=(--volume "$path:$path")
done

# Work where the caller works (moon: the project's root; the editor: the file's
# directory) so that relative paths resolve, unless that is outside the mounts.
workdir="$PWD"
case "$workdir/" in "$root"/*) ;; *) workdir="$root" ;; esac

# Only with a terminal: --tty merges stderr into stdout, and the editor parses
# stdout (--format=json).
tty=()
if [[ -t 1 ]]; then
  tty=(--tty)
fi

# ${tty[@]+...}: macOS's bash 3.2 treats an empty array as unbound under set -u.
# The files are written as the caller, not root (Linux). No network: the
# parallel runner only talks over the loopback.
exec docker run --rm ${tty[@]+"${tty[@]}"} \
  --user "$(id -u):$(id -g)" \
  --network none \
  --env PHP_CS_FIXER_IGNORE_ENV \
  "${mounts[@]}" \
  --workdir "$workdir" \
  "$image" "$@"
