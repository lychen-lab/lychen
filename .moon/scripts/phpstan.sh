#!/usr/bin/env bash
# PHPStan for the whole monorepo: one install (tools/php: PHPStan, its extensions,
# Rector), run by the PHP of .prototools on the host. Every PHP project runs it
# with its own phpstan.dist.neon and baseline: the moon lint tasks, from the
# project's root, and the editor (sanderronde.phpstan-vscode), with an absolute
# -c from the workspace's root. Takes the arguments of phpstan itself.
#
# PHPStan runs in the directory of its configuration (or where it is called
# from, without -c): it loads that project's vendor/autoload.php from there. A
# project's vendor/ comes from its container (an API's entrypoint runs composer
# install into an empty vendor/), so an API is analysed with the dependencies it
# runs with. PHPStan needs none of the PHP extensions the APIs load: it reads
# their stubs.
set -euo pipefail

root="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
tools="$root/tools/php"

# The PHP pinned in .prototools. Pinned here too, since proto would otherwise
# take the `require.php` of the project's composer.json (">=8.4") from there.
PROTO_PHP_VERSION="$(sed -n 's/^php = "\(.*\)"$/\1/p' "$root/.prototools")"
export PROTO_PHP_VERSION

# Installs the tools on first use, and again whenever their composer.lock
# changes, with Composer from its official image. Everything it prints goes to
# stderr: the editor parses PHPStan's stdout (--error-format=json).
if ! cmp -s "$tools/composer.lock" "$tools/vendor/.composer.lock"; then
  # The files are written as the caller, not root (Linux), whose home the
  # container doesn't have: Composer's cache goes to a temporary home.
  docker run --rm \
    --user "$(id -u):$(id -g)" \
    --env COMPOSER_HOME=/tmp/composer \
    --volume "$tools:$tools" \
    --workdir "$tools" \
    composer:2.11.2 install --no-interaction --no-progress >&2
  cp "$tools/composer.lock" "$tools/vendor/.composer.lock"
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

# ${args[@]+...}: macOS's bash 3.2 treats an empty array as unbound under set -u.
exec php "$tools/vendor/bin/phpstan" ${args[@]+"${args[@]}"}
