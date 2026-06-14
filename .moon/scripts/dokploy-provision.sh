#!/bin/bash
#
# Idempotent provisioning of a Dokploy compose service + its public domain.
#
# Given a moon project, this ensures the following exist on the Dokploy server
# (creating whatever is missing) and resolves the compose id used by the deploy:
#
#   Dokploy project   == $DOKPLOY_PROJECT          (the "domain group": tera, espace, ...)
#     └─ environment   == $DOKPLOY_ENVIRONMENT      (staging | production)
#         └─ compose    == $MOON_PROJECT_ID          (e.g. tera-api, website)
#             ├─ source  == GitHub provider, composePath ./<src>/compose.<env>.yml
#             └─ domain  == $STAGING_DOMAIN / $PRODUCTION_DOMAIN (optional)
#
# The compose is wired to the GitHub provider so Dokploy pulls the per-environment
# compose file from git itself (compose.staging.yml / compose.prod.yml) and
# deploys it on `compose.deploy` — nothing is uploaded. Everything is resolved
# *by name* so no compose ids need to be stored anywhere; the resolved id is
# written to $COMPOSE_ID_FILE for the dokploy-compose-deploy task.
#
# This script only talks to the Dokploy API. DNS records are handled separately
# by cloudflare-provision.sh.
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

# GitHub provider config — Dokploy clones the repo at $GIT_BRANCH and deploys the
# compose file at $COMPOSE_PATH (relative to the repo root). production -> prod.
GIT_OWNER="${DOKPLOY_GIT_OWNER:-lychen-lab}"
GIT_REPOSITORY="${DOKPLOY_GIT_REPOSITORY:-lychen}"
GIT_BRANCH="${DOKPLOY_GIT_BRANCH:-main}"
case "$DOKPLOY_ENVIRONMENT" in
  production) COMPOSE_ENV_SUFFIX="prod" ;;
  *) COMPOSE_ENV_SUFFIX="$DOKPLOY_ENVIRONMENT" ;;
esac
PROJECT_SOURCE="${MOON_PROJECT_SOURCE:-${MOON_PROJECT_ROOT#"${MOON_WORKSPACE_ROOT:-}"/}}"
COMPOSE_PATH="./${PROJECT_SOURCE}/compose.${COMPOSE_ENV_SUFFIX}.yml"

# Domain for this environment (either may be unset -> no domain).
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

# Resolve the Dokploy GitHub provider id. Matches DOKPLOY_GITHUB_PROVIDER against
# the provider display name, or auto-selects when exactly one is configured.
resolve_github_id() {
  local providers
  providers="$(dokploy_api GET "/github.githubProviders")" || return 1
  if [ -n "${DOKPLOY_GITHUB_PROVIDER:-}" ]; then
    printf '%s' "$providers" | jq -r --arg n "$DOKPLOY_GITHUB_PROVIDER" '[.[]? | select(.gitProvider.name == $n)] | .[0].githubId // empty'
  else
    printf '%s' "$providers" | jq -r 'if (type == "array" and length == 1) then .[0].githubId else empty end'
  fi
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

# Hand the resolved id to the deploy task.
printf '%s' "$compose_id" > "$COMPOSE_ID_FILE"

# --- 4. Wire the GitHub provider --------------------------------------------
# compose.create cannot set git fields, so configure the source here. Idempotent:
# re-running just re-asserts the same provider config.
echo "> Wiring GitHub provider -> ${GIT_OWNER}/${GIT_REPOSITORY}@${GIT_BRANCH} : ${COMPOSE_PATH}"
github_id="$(resolve_github_id)"
[ -n "$github_id" ] || { echo "Error: no Dokploy GitHub provider resolved - set DOKPLOY_GITHUB_PROVIDER to the provider's display name" >&2; exit 1; }
dokploy_api POST "/compose.update" \
  "$(jq -nc \
      --arg id "$compose_id" \
      --arg gh "$github_id" \
      --arg owner "$GIT_OWNER" \
      --arg repo "$GIT_REPOSITORY" \
      --arg branch "$GIT_BRANCH" \
      --arg path "$COMPOSE_PATH" \
      '{composeId: $id, sourceType: "github", githubId: $gh, owner: $owner, repository: $repo, branch: $branch, composePath: $path, triggerType: "tag", autoDeploy: true, enableSubmodules: false}')" >/dev/null
echo "  - provider set (githubId: $github_id)"

# --- 5. Ensure the domain (optional) ----------------------------------------
if [ -z "$DOMAIN" ]; then
  echo "> No domain configured for $DOKPLOY_ENVIRONMENT (set STAGING_DOMAIN/PRODUCTION_DOMAIN to enable) - skipping domain"
  echo "> Dokploy provisioning complete"
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

echo "> Dokploy provisioning complete"
