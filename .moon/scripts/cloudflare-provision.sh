#!/bin/bash
#
# Cloudflare DNS provisioning for a moon project's environment.
#
# Upserts a DNS-only (grey-cloud) A record for the environment's host
# ($STAGING_DOMAIN / $PRODUCTION_DOMAIN) pointing at the Dokploy server
# ($DOKPLOY_SERVER_IPV4). This is intentionally independent of Dokploy: it only
# touches Cloudflare, so it can run (or be skipped) on its own.
#
# Skipped gracefully when there is no host for the environment or no Cloudflare
# token, so projects without a domain yet do not fail the pipeline.
#
set -euo pipefail

: "${DOKPLOY_ENVIRONMENT:?DOKPLOY_ENVIRONMENT is required (staging|production)}"
case "$DOKPLOY_ENVIRONMENT" in
  staging | production) ;;
  *) echo "Error: DOKPLOY_ENVIRONMENT must be 'staging' or 'production', got '$DOKPLOY_ENVIRONMENT'" >&2; exit 1 ;;
esac

# Host for this environment (either may be unset -> nothing to do).
if [ "$DOKPLOY_ENVIRONMENT" = "production" ]; then
  DOMAIN="${PRODUCTION_DOMAIN:-}"
else
  DOMAIN="${STAGING_DOMAIN:-}"
fi

if [ -z "$DOMAIN" ]; then
  echo "> No domain configured for $DOKPLOY_ENVIRONMENT (set STAGING_DOMAIN/PRODUCTION_DOMAIN to enable) - skipping Cloudflare DNS"
  exit 0
fi
if [ -z "${CLOUDFLARE_API_TOKEN:-}" ]; then
  echo "> CLOUDFLARE_API_TOKEN not set - skipping Cloudflare DNS for $DOMAIN"
  exit 0
fi
: "${DOKPLOY_SERVER_IPV4:?DOKPLOY_SERVER_IPV4 is required to create DNS records}"

CF_API="https://api.cloudflare.com/client/v4"
CLOUDFLARE_PROXIED="${CLOUDFLARE_PROXIED:-false}"
# Registrable zone = last two labels of the host (e.g. lychen.org), overridable.
zone_name="${CLOUDFLARE_ZONE_NAME:-$(printf '%s' "$DOMAIN" | awk -F. '{print $(NF-1)"."$NF}')}"

# cloudflare_api <GET|POST|PUT> <path> [json-body]
cloudflare_api() {
  local method="$1" path="$2" body="${3:-}"
  local curl_args=(
    -sS -X "$method" "${CF_API}${path}"
    -H "Authorization: Bearer ${CLOUDFLARE_API_TOKEN}"
    -H "Content-Type: application/json"
    -w $'\n%{http_code}'
  )
  if [ -n "$body" ]; then curl_args+=(-d "$body"); fi

  local response http_code payload
  response="$(curl "${curl_args[@]}")" || { echo "Error: curl failed for Cloudflare $method $path" >&2; return 1; }
  http_code="$(printf '%s' "$response" | tail -n1)"
  payload="$(printf '%s' "$response" | sed '$d')"
  if [ "$http_code" -ge 400 ] || [ "$(printf '%s' "$payload" | jq -r '.success')" != "true" ]; then
    echo "Error: Cloudflare API $method $path -> HTTP $http_code" >&2
    echo "       $payload" >&2
    return 1
  fi
  printf '%s' "$payload"
}

echo "> Upserting Cloudflare A record '$DOMAIN' -> $DOKPLOY_SERVER_IPV4 (zone: $zone_name, proxied: $CLOUDFLARE_PROXIED)"
zone_id="$(cloudflare_api GET "/zones?name=${zone_name}" | jq -r '.result[0].id // empty')"
[ -n "$zone_id" ] || { echo "Error: Cloudflare zone '$zone_name' not found (check token zone scope)" >&2; exit 1; }

record_id="$(cloudflare_api GET "/zones/${zone_id}/dns_records?type=A&name=${DOMAIN}" | jq -r '.result[0].id // empty')"
record_body="$(jq -nc \
  --arg name "$DOMAIN" \
  --arg content "$DOKPLOY_SERVER_IPV4" \
  --argjson proxied "$CLOUDFLARE_PROXIED" \
  '{type: "A", name: $name, content: $content, ttl: 1, proxied: $proxied}')"

if [ -n "$record_id" ]; then
  echo "  - updating existing record"
  cloudflare_api PUT "/zones/${zone_id}/dns_records/${record_id}" "$record_body" >/dev/null
else
  echo "  - creating record"
  cloudflare_api POST "/zones/${zone_id}/dns_records" "$record_body" >/dev/null
fi

echo "> Cloudflare DNS up to date"
