#!/usr/bin/env bash
# PHPStan for the whole monorepo, from its own image (tools/phpstan/Dockerfile:
# the official PHP image with PHPStan, its extensions and Rector installed). It
# needs Docker, not PHP on the host nor the API's container, as php-cs-fixer
# (.moon/scripts/php-cs-fixer.sh). Every PHP project runs it with its own
# phpstan.dist.neon and baseline: the moon lint tasks, from the project's root,
# and the editor (sanderronde.phpstan-vscode), with an absolute -c from the
# workspace's root. Takes the arguments of phpstan itself.
#
# The image is built on first use, and again when tools/phpstan changes (its tag
# is a hash of the Dockerfile and composer.lock), replacing the previous build.
# Nothing stays running: each call is a `docker run --rm`. To start over, remove
# the image (docker rmi lychen-local/phpstan:<tag>) and PHPStan's result cache
# (.moon/cache/phpstan).
#
# PHPStan runs in the directory of its configuration (or where it is called
# from, without -c): it loads that project's vendor/autoload.php from there. A
# project's vendor/ comes from its container (an API's entrypoint runs composer
# install into an empty vendor/), so an API is analysed with the dependencies it
# runs with. PHPStan needs none of the PHP extensions the APIs load: it reads
# their stubs.
set -euo pipefail

root="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
tools="$root/tools/phpstan"
# PHPStan's result cache (/tmp in the container), kept between runs so that a
# save only reanalyses what changed.
cache="$root/.moon/cache/phpstan"

# The image, rebuilt when its inputs change. Everything the build prints goes to
# stderr: the editor parses PHPStan's stdout (--error-format=json).
hash="$(cat "$tools/Dockerfile" "$tools/composer.lock" | (shasum -a 256 2>/dev/null || sha256sum) | cut -c1-12)"
image="lychen-local/phpstan:$hash"
if ! docker image inspect "$image" >/dev/null 2>&1; then
  docker build --quiet --tag "$image" "$tools" >&2
  docker images --filter reference=lychen-local/phpstan --format '{{.Repository}}:{{.Tag}}' \
    | grep -v -x -F "$image" | xargs -r docker rmi >/dev/null 2>&1 || true
fi

# The configuration's path is made absolute before moving to its directory, so
# that it still resolves from there.
absolute() {
  echo "$(cd "$(dirname "$1")" && pwd)/$(basename "$1")"
}
args=()
workdir=''
next_is_config=false
for arg in "$@"; do
  if $next_is_config; then
    arg="$(absolute "$arg")"
    workdir="$(dirname "$arg")"
    next_is_config=false
  else
    case "$arg" in
      -c | --configuration) next_is_config=true ;;
      --configuration=*)
        arg="--configuration=$(absolute "${arg#*=}")"
        workdir="$(dirname "${arg#*=}")"
        ;;
    esac
  fi
  args+=("$arg")
done
if [[ -n "$workdir" ]]; then
  cd "$workdir"
fi
# The container only sees the repository: a call from outside it works from its
# root.
case "$PWD/" in "$root"/*) ;; *) cd "$root" ;; esac

# Without its dependencies, PHPStan would report every class they define as
# unknown: a thousand errors that say nothing about the code. Only the
# project's container installs them, so this says how instead of running.
# stderr only: the editor shows it in its log (and must not read "No such
# file" in it, its "PHP binary not found" case).
if [[ -f composer.json ]]; then
  project="${PWD#"$root"/}"
  id="$(basename "$(dirname "$PWD")")-$(basename "$PWD")"
  restart="moon $id:down, then moon $id:dev"
  if [[ ! -f vendor/autoload.php ]]; then
    echo "PHPStan not run: $project has no vendor/. Its container installs it" \
      "when it starts with an empty vendor/: start it with moon $id:dev (if" \
      "it already runs: $restart)." >&2
    exit 1
  fi
  # -L: a link is still a link only if its target is missing. Typically an
  # old install's lychen/* bundles, linked to ../packages: a folder of the
  # container only.
  broken="$(find -L vendor -mindepth 2 -maxdepth 2 -type l -print -quit)"
  if [[ -n "$broken" ]]; then
    echo "PHPStan not run: $project/$broken links to $(readlink "$broken")," \
      "which exists only in its container. Delete $project/vendor/, then" \
      "restart the container ($restart): it reinstalls it." >&2
    exit 1
  fi
fi

mkdir -p "$cache"

# Only with a terminal: --tty merges stderr into stdout, and the editor parses
# stdout.
tty=()
if [[ -t 1 ]]; then
  tty=(--tty)
fi

# The repository is mounted at its own path, so every path the caller passes
# (the config, a file, a project's root) means the same in the container, and
# the paths PHPStan reports are the caller's. The files are written as the
# caller, not root (Linux). No network: the parallel runner only talks over the
# loopback.
# ${tty[@]+...}: macOS's bash 3.2 treats an empty array as unbound under set -u.
exec docker run --rm ${tty[@]+"${tty[@]}"} \
  --user "$(id -u):$(id -g)" \
  --network none \
  --volume "$root:$root" \
  --volume "$cache:/tmp" \
  --workdir "$PWD" \
  "$image" ${args[@]+"${args[@]}"}
