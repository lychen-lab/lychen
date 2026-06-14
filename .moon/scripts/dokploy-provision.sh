#!/bin/bash
#
# Idempotent provisioning of a Dokploy compose service + its public domain + DNS.
#
# Given a moon project, this ensures the following exist on the Dokploy server
# (creating whatever is missing) and resolves the compose id used by the deploy:
#
#   Dokploy project   == $DOKPLOY_PROJECT          (the "domain group": tera, espace, ...)
#     └─ environment   == $DOKPLOY_ENVIRONMENT      (staging | production)
#         └─ compose    == $MOON_PROJECT_ID          (e.g. tera-api, website)
#             └─ domain  == $STAGING_DOMAIN / $PRODUCTION_DOMAIN (optional)
#
# Everything is resolved *by name* so no compose ids need to be stored anywhere.
# The resolved compose id is written to $COMPOSE_ID_FILE for the subsequent
# dokploy-compose-update / dokploy-compose-deploy tasks to consume.
#
# When a domain is configured for the environment it is attached to the compose
# (Traefik route) and a DNS-only A record is upserted in Cloudflare pointing at
# the Dokploy server. Both steps are skipped gracefully when their inputs are
# absent, so projects without a domain yet still get their structure created.
#
set -euo pipefail

# --- Required inputs ---------------------------------------------------------
: "${DOKPLOY_API_URL:?DOKPLOY_API_URL is required}"
: "${DOKPLOY_API_TOKEN:?DOKPLOY_API_TOKEN is required}"
: "${DOKPLOY_ENVIRONMENT:?DOKPLOY_ENVIRONMENT is required (staging|production)}"
: "${DOKPLOY_PROJECT:?DOKPLOY_PROJECT is required (the Dokploy project / domain group name)}"
: "${MOON_PROJECT_ID:?MOON_PROJECT_ID is required (used as the compose service name)}"
: "${COMPOSE_ID_FILE:?COMPOSE_ID_FILE is required (where to write the resolved compose id)}"

case "$DOKPLOY_ENVIRONMENT" in
  staging | production) ;;
  *) echo "Error: DOKPLOY_ENVIRONMENT must be 'staging' or 'production', got '$DOKPLOY_ENVIRONMENT'" >&2; exit 1 ;;
esac

COMPOSE_NAME="$MOON_PROJECT_ID"

# Pick the domain for this environment (either may be unset -> no domain).
if [ "$DOKPLOY_ENVIRONMENT" = "production" ]; then
  DOMAIN="${PRODUCTION_DOMAIN:-}"
else
  DOMAIN="${STAGING_DOMAIN:-}"
fi
DOMAIN_SERVICE="${DOMAIN_SERVICE:-}"
DOMAIN_PORT="${DOMAIN_PORT:-80}"

# --- Dokploy API helper ------------------------------------------------------
# dokploy_api <GET|POST> <path-with-leading-slash> [json-body]
dokploy_api() {
  local method="$1" path="$2" body="${3:-}"
  local curl_args=(
    -sS -X "$method" "${DOKPLOY_API_URL}${path}"
    -H "accept: application/json"
    -H "x-api-key: ${DOKPLOY_API_TOKEN}"
    -w $'\n%{http_code}'
  )
  if [ -n "$body" ]; then
    curl_args+=(-H "Content-Type: application/json" -d "$body")
  fi

  local response http_code payload
  response="$(curl "${curl_args[@]}")" || { echo "Error: curl failed for $method $path" >&2; return 1; }
  http_code="$(printf '%s' "$response" | tail -n1)"
  payload="$(printf '%s' "$response" | sed '$d')"

  if [ "$http_code" -ge 400 ]; then
    echo "Error: Dokploy API $method $path -> HTTP $http_code" >&2
    echo "       $payload" >&2
    return 1
  fi
  printf '%s' "$payload"
}

# --- 1. Ensure the project ---------------------------------------------------
echo "> Ensuring Dokploy project '$DOKPLOY_PROJECT'"
# project.all may return a bare array or a { projects: [...] } wrapper; tolerate both.
project_id="$(dokploy_api GET "/project.all" \
  | jq -r --arg n "$DOKPLOY_PROJECT" '(if type == "array" then . else (.projects // []) end) | map(select(.name == $n)) | .[0].projectId // empty')"

if [ -z "$project_id" ]; then
  echo "  - not found, creating it"
  dokploy_api POST "/project.create" \
    "$(jq -nc --arg n "$DOKPLOY_PROJECT" '{name: $n, description: "Managed by CI", env: ""}')" >/dev/null
  project_id="$(dokploy_api GET "/project.all" \
    | jq -r --arg n "$DOKPLOY_PROJECT" '(if type == "array" then . else (.projects // []) end) | map(select(.name == $n)) | .[0].projectId // empty')"
fi
[ -n "$project_id" ] || { echo "Error: could not resolve projectId for '$DOKPLOY_PROJECT'" >&2; exit 1; }
echo "  - projectId: $project_id"

# --- 2. Ensure the environment ----------------------------------------------
# A new project is created with a default environment named exactly "production".
echo "> Ensuring environment '$DOKPLOY_ENVIRONMENT'"
resolve_environment_id() {
  dokploy_api GET "/environment.byProjectId?projectId=${project_id}" \
    | jq -r --arg n "$DOKPLOY_ENVIRONMENT" '(if type == "array" then . else (.environments // []) end) | map(select((.name | ascii_downcase) == ($n | ascii_downcase))) | .[0].environmentId // empty'
}
environment_id="$(resolve_environment_id)"

if [ -z "$environment_id" ]; then
  echo "  - not found, creating it"
  dokploy_api POST "/environment.create" \
    "$(jq -nc --arg n "$DOKPLOY_ENVIRONMENT" --arg p "$project_id" '{name: $n, projectId: $p, description: "Managed by CI"}')" >/dev/null
  environment_id="$(resolve_environment_id)"
fi
[ -n "$environment_id" ] || { echo "Error: could not resolve environmentId for '$DOKPLOY_ENVIRONMENT'" >&2; exit 1; }
echo "  - environmentId: $environment_id"

# --- 3. Ensure the compose service ------------------------------------------
echo "> Ensuring compose service '$COMPOSE_NAME'"
resolve_compose_id() {
  dokploy_api GET "/project.one?projectId=${project_id}" \
    | jq -r \
        --arg env "$DOKPLOY_ENVIRONMENT" \
        --arg name "$COMPOSE_NAME" \
        '[.environments[]? | select((.name | ascii_downcase) == ($env | ascii_downcase)) | .compose[]? | select(.name == $name) | .composeId] | .[0] // empty'
}
compose_id="$(resolve_compose_id)"

if [ -z "$compose_id" ]; then
  echo "  - not found, creating it"
  dokploy_api POST "/compose.create" \
    "$(jq -nc --arg n "$COMPOSE_NAME" --arg e "$environment_id" \
        '{name: $n, description: "Managed by CI", environmentId: $e, composeType: "docker-compose"}')" >/dev/null
  compose_id="$(resolve_compose_id)"
fi
[ -n "$compose_id" ] || { echo "Error: could not resolve composeId for '$COMPOSE_NAME'" >&2; exit 1; }
echo "  - composeId: $compose_id"

# Hand the resolved id to the update/deploy tasks.
printf '%s' "$compose_id" > "$COMPOSE_ID_FILE"

# --- 4. Ensure the domain (optional) ----------------------------------------
if [ -z "$DOMAIN" ]; then
  echo "> No domain configured for $DOKPLOY_ENVIRONMENT (set STAGING_DOMAIN/PRODUCTION_DOMAIN to enable) - skipping domain + DNS"
  echo "> Provisioning complete"
  exit 0
fi

[ -n "$DOMAIN_SERVICE" ] || { echo "Error: DOMAIN is set ($DOMAIN) but DOMAIN_SERVICE is empty" >&2; exit 1; }

echo "> Ensuring domain '$DOMAIN' -> service '$DOMAIN_SERVICE:$DOMAIN_PORT'"
# Dokploy has no uniqueness check on host, so de-dupe ourselves before creating.
existing_host="$(dokploy_api GET "/domain.byComposeId?composeId=${compose_id}" \
  | jq -r --arg h "$DOMAIN" '(if type == "array" then . else (.domains // []) end) | map(select(.host == $h)) | .[0].host // empty')"

if [ -n "$existing_host" ]; then
  echo "  - already attached"
else
  echo "  - attaching"
  dokploy_api POST "/domain.create" \
    "$(jq -nc \
        --arg host "$DOMAIN" \
        --arg composeId "$compose_id" \
        --arg serviceName "$DOMAIN_SERVICE" \
        --argjson port "$DOMAIN_PORT" \
        '{host: $host, composeId: $composeId, serviceName: $serviceName, port: $port, domainType: "compose", https: true, certificateType: "letsencrypt"}')" >/dev/null
fi

# --- 5. Upsert the Cloudflare DNS record (optional) --------------------------
if [ -z "${CLOUDFLARE_API_TOKEN:-}" ]; then
  echo "> CLOUDFLARE_API_TOKEN not set - skipping DNS record for $DOMAIN"
  echo "> Provisioning complete"
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

echo "> Provisioning complete"
