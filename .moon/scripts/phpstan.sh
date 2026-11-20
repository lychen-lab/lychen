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

# Said plainly, on stderr: the editor shows any other wording of a missing
# docker as "PHP binary not found" (it reads "command not found" / "No such
# file" that way), which points away from the cause.
if ! command -v docker >/dev/null 2>&1; then
  echo "PHPStan not run: docker is not in PATH ($PATH). PHPStan runs from a" \
    "Docker image here: install Docker, or start the editor from a shell" \
    "that has it." >&2
  exit 1
fi

# The image's inputs, named plainly when one is missing (a copy of this runner
# into another repository without them), for the same reason as docker above.
for input in Dockerfile composer.json composer.lock; do
  if [[ ! -f "$tools/$input" ]]; then
    echo "PHPStan not run: $tools/$input is missing. The image is built from" \
      "tools/phpstan/Dockerfile, composer.json and composer.lock." >&2
    exit 1
  fi
done

# The image, rebuilt when its inputs change. Everything the build prints goes to
# stderr: the editor parses PHPStan's stdout (--error-format=json).
# sha256sum is coreutils' (Linux), shasum is macOS's.
sha256() {
  if command -v sha256sum >/dev/null 2>&1; then
    sha256sum
  else
    shasum -a 256
  fi
}
hash="$(cat "$tools/Dockerfile" "$tools/composer.lock" | sha256 | cut -c1-12)"
image="lychen-local/phpstan:$hash"
if ! docker image inspect "$image" >/dev/null 2>&1; then
  # BuildKit, which the Dockerfile's RUN --mount needs: the default since
  # Docker 23, not on every Linux.
  DOCKER_BUILDKIT=1 docker build --quiet --tag "$image" "$tools" >&2
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
  # The path packages: in the container, /packages, libs/php mounted in
  # development (compose.yml), a copy baked into the test and production
  # images (copy-packages). Installed as links (symlink: true in
  # .moon/toolchains.yml, written into every composer.json by moon's composer
  # toolchain: a library change is live in the API), vendor/ points to
  # ../packages, a folder of the container only: the host gets it too, next to
  # the API, as a link to libs/php (gitignored), so that vendor/ resolves here
  # as there, for PHPStan and the editor alike. Any other link whose target is
  # missing is an install the container must redo.
  packages="$(dirname "$PWD")/packages"
  while IFS= read -r link; do
    target="$(readlink "$link")"
    case "$target" in
      ../../../packages/*)
        if [[ -L "$packages" && ! -e "$packages" ]]; then
          rm "$packages"
        fi
        if [[ ! -e "$packages" ]]; then
          # Relative, so that it survives a move of the checkout.
          IFS=/ read -ra depth <<< "${packages#"$root"/}"
          up=""
          for ((i = 1; i < ${#depth[@]}; i++)); do up+="../"; done
          ln -s "${up}libs/php" "$packages"
        fi
        lib="${target#../../../packages/}"
        lib="${lib%%/*}"
        if [[ ! -d "$packages/$lib" ]]; then
          echo "PHPStan not run: $project/$link links to $target, which" \
            "${packages#"$root"/} lacks. Delete $project/vendor/, then restart" \
            "the container ($restart): it reinstalls it." >&2
          exit 1
        fi
        ;;
      *)
        if [[ ! -e "$link" ]]; then
          echo "PHPStan not run: $project/$link links to $target, which" \
            "exists only in its container. Delete $project/vendor/, then" \
            "restart the container ($restart): it reinstalls it." >&2
          exit 1
        fi
        ;;
    esac
  done < <(find vendor -mindepth 2 -maxdepth 2 -type l)
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
