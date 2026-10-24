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
# The hub speaks the Mercure 1.0 protocol, where tokens are OAuth 2.0 access
# tokens (RFC 9068), and each tenant pins the two values its tokens are checked
# against:
#
# - its issuer, `urn:lychen:<tenant>-api`: the `iss` its API signs with, bound to
#   that tenant's keys. A fixed identifier rather than a URL, since nothing has
#   to discover it — the keys, not the name, keep tenants apart.
# - its resource identifier, `$MERCURE_PUBLIC_ORIGIN/<tenant>/.well-known/mercure`:
#   the `aud` its tokens must carry. Left to itself the hub would derive one per
#   request as `<origin>/.well-known/mercure`, which drops the tenant prefix
#   `handle_path` strips — and would differ between the internal URL the APIs
#   publish to and the public one browsers subscribe to.
#
# Secrets never touch the disk: the rendered file refers to the keys as
# `{env.MERCURE_<TENANT>_PUBLISHER_JWT_KEY}`, which Caddy resolves at runtime.
set -eu

TENANTS="${MERCURE_TENANTS:-tera espace flora}"
CADDYFILE="${MERCURE_CADDYFILE:-/etc/caddy/lychen.Caddyfile}"
JWT_ALG="${MERCURE_JWT_ALG:-HS256}"
# Where browsers reach the hub, without any tenant prefix (`https://mercure.example.org`).
# No default: a wrong one only shows up as every token being turned down.
PUBLIC_ORIGIN="${MERCURE_PUBLIC_ORIGIN:-}"
PUBLIC_ORIGIN="${PUBLIC_ORIGIN%/}"
case "$PUBLIC_ORIGIN" in
  http://?* | https://?*) ;;
  *)
    echo "lychen: MERCURE_PUBLIC_ORIGIN must be the hub's public origin, e.g. https://mercure.example.org (got '$PUBLIC_ORIGIN')" >&2
    exit 1
    ;;
esac

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
				# The 1.0 protocol never puts a token in the URL, and the hub turns
				# one down there, but a client still on 0.x (a PWA serving a stale
				# bundle, say) sends it anyway: keep it out of the logs.
				request>uri query {
					replace authorization REDACTED
				}
			}
		}
	}

	# No `encode`: Caddy compresses an event stream with one encoder per subscriber,
	# kept for the whole connection (megabytes each), and a compression context
	# shared between private updates and content other users wrote is what BREACH
	# needs. The hub's own Caddyfile leaves it off for the same reasons.
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
			# One Caddy process runs every hub, so each needs a name — a distinct
			# one: hubs with the same name share a transport.
			name $tenant
			transport bolt {
				path /data/$tenant.db
			}
			issuer urn:lychen:$tenant-api {
				publisher {
					jwt {env.$publisher_var} $JWT_ALG
				}
				subscriber {
					jwt {env.$subscriber_var} $JWT_ALG
				}
			}
			resource_identifier $PUBLIC_ORIGIN/$tenant/.well-known/mercure
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
  echo "lychen:   $PUBLIC_ORIGIN/$tenant/.well-known/mercure (iss urn:lychen:$tenant-api)"
done
if [ -n "$seeded" ]; then
  # Loud on purpose: these are public, in-repo values. Anything but local dev
  # must set MERCURE_<TENANT>_PUBLISHER_JWT_KEY / _SUBSCRIBER_JWT_KEY.
  echo "lychen: WARNING using built-in development JWT keys for:$seeded" >&2
fi

exec "$@"
