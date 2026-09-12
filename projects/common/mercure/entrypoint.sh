#!/bin/sh
# Render the hub's Caddyfile from the environment, then hand over to Caddy.
#
# Why render it instead of committing a Caddyfile: the Mercure Caddy module is
# configured *per site*, and one central hub serving every API has to be
# multi-tenant — each API gets its own JWT keys and its own transport, so the
# identically-shaped topics the APIs publish cannot collide and no API can
# subscribe to another's updates. That is the same isolation the central broker
# gets from per-vhost credentials (`projects/common/rabbitmq`). A Caddyfile
# cannot loop over a list, so the per-tenant blocks are generated here from
# `MERCURE_TENANTS`.
#
# Each tenant is mounted under its own path prefix, so one port serves them all
# and adding a tenant needs no new port and no new domain:
#
#     http://mercure/<tenant>/.well-known/mercure
#
# Secrets never touch the disk: the rendered file refers to the keys as
# `{env.MERCURE_<TENANT>_PUBLISHER_JWT_KEY}`, which Caddy resolves at runtime.
set -eu

TENANTS="${MERCURE_TENANTS:-tera espace flora}"
CADDYFILE="${MERCURE_CADDYFILE:-/etc/caddy/lychen.Caddyfile}"
JWT_ALG="${MERCURE_JWT_ALG:-HS256}"

# env_name <tenant> <suffix> -> MERCURE_<TENANT>_<SUFFIX>
env_name() {
  printf 'MERCURE_%s_%s' "$(printf '%s' "$1" | tr 'a-z-' 'A-Z_')" "$2"
}

# read_var <name> — value of the variable called <name>, empty when unset.
read_var() {
  eval "printf '%s' \"\${$1:-}\""
}

seeded=''

# Pass 1 — validate the tenants and give their keys a dev-only default, so a
# fresh checkout boots with no configuration at all. This has to happen in *this*
# shell: done inside a command substitution the `export` would be confined to the
# subshell, and `{env.…}` would resolve to an empty key at runtime.
for tenant in $TENANTS; do
  # Tenant names end up in a URL path, a file name and an env var name, so keep
  # them to the lowercase form every API project id already uses.
  case "$tenant" in
    *[!a-z0-9-]* | -* | *- | '')
      echo "lychen: invalid tenant name '$tenant' (expected [a-z0-9-])" >&2
      exit 1
      ;;
  esac

  for role in publisher subscriber; do
    key_var="$(env_name "$tenant" "$(printf '%s' "$role" | tr 'a-z' 'A-Z')_JWT_KEY")"
    if [ -z "$(read_var "$key_var")" ]; then
      export "$key_var=!ChangeThisDevKey-$tenant-$role!"
      seeded="$seeded $tenant/$role"
    fi
  done
done

# Pass 2 — render the Caddyfile.
{
  cat <<'CADDY'
# Generated at boot by entrypoint.sh — edit that script, never this file.
{
	{$GLOBAL_OPTIONS}
}

{$CADDY_EXTRA_CONFIG}

{$SERVER_NAME::80} {
	log {
		format filter {
			fields {
				# Subscribers may pass their JWT as a query parameter.
				request>uri query {
					replace authorization REDACTED
				}
			}
		}
	}

	encode zstd gzip
CADDY

  for tenant in $TENANTS; do
    publisher_var="$(env_name "$tenant" PUBLISHER_JWT_KEY)"
    subscriber_var="$(env_name "$tenant" SUBSCRIBER_JWT_KEY)"
    # Directives shared by every tenant, then the ones for this tenant alone
    # (production CORS origins differ per app domain, for instance).
    extra="${MERCURE_EXTRA_DIRECTIVES:-}
$(read_var "$(env_name "$tenant" EXTRA_DIRECTIVES)")"

    cat <<CADDY

	# --- $tenant -> /$tenant/.well-known/mercure ---
	handle_path /$tenant/* {
		mercure {
			transport bolt {
				path /data/$tenant.db
			}
			publisher_jwt {env.$publisher_var} $JWT_ALG
			subscriber_jwt {env.$subscriber_var} $JWT_ALG
$(printf '%s\n' "$extra" | sed -e 's/^[[:space:]]*//' -e '/^$/d' -e 's/^/\t\t\t/')
		}
	}
CADDY
  done

  cat <<'CADDY'

	respond /robots.txt `User-agent: *
Disallow: /`
	respond "Not Found" 404
}
CADDY
} >"$CADDYFILE"

echo "lychen: serving mercure hubs for [$TENANTS]"
for tenant in $TENANTS; do
  echo "lychen:   /$tenant/.well-known/mercure"
done
if [ -n "$seeded" ]; then
  # Loud on purpose: these are public, in-repo values. Anything but local dev
  # must set MERCURE_<TENANT>_PUBLISHER_JWT_KEY / _SUBSCRIBER_JWT_KEY.
  echo "lychen: WARNING using built-in development JWT keys for:$seeded" >&2
fi

exec "$@"
