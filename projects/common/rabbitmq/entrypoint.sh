#!/bin/sh
# Render the RabbitMQ definitions file from the environment, then hand over to
# the image's own entrypoint.
#
# Why render it instead of committing a definitions.json: a definitions file
# cannot interpolate environment variables, and RABBITMQ_DEFAULT_USER/PASS/VHOST
# are no alternative — they only ever seed a *single* vhost, and the node skips
# them outright as soon as it has definitions to import ("Will not seed default
# virtual host and user: have definitions to load..."). Rendering at boot is what
# lets this one compose provision dev, staging and production, each with its own
# credentials.
#
# Definitions are re-imported on every boot and are idempotent: vhosts and users
# that already exist are updated in place, queues and messages are untouched.
set -eu

TENANTS="${RABBITMQ_TENANTS:-tera espace flora}"
ADMIN_USER="${RABBITMQ_ADMIN_USER:-admin}"
ADMIN_PASSWORD="${RABBITMQ_ADMIN_PASSWORD:-admin}"
DEFINITIONS_FILE="${RABBITMQ_DEFINITIONS_FILE:-/etc/rabbitmq/definitions.json}"

# JSON-escape a value, so a password containing " or \ cannot break the document.
json_escape() {
  printf '%s' "$1" | sed -e 's/\\/\\\\/g' -e 's/"/\\"/g'
}

# tenant_password <tenant> — $RABBITMQ_<TENANT>_PASSWORD, falling back to the
# tenant name so a fresh checkout boots with no configuration at all.
tenant_password() {
  _var="RABBITMQ_$(printf '%s' "$1" | tr 'a-z-' 'A-Z_')_PASSWORD"
  eval "_value=\${$_var:-}"
  [ -n "$_value" ] || _value="$1"
  printf '%s' "$_value"
}

admin="$(json_escape "$ADMIN_USER")"
users="{\"name\":\"$admin\",\"password\":\"$(json_escape "$ADMIN_PASSWORD")\",\"tags\":[\"administrator\"]}"
vhosts=""
permissions=""
separator=""

for tenant in $TENANTS; do
  name="$(json_escape "$tenant")"
  password="$(json_escape "$(tenant_password "$tenant")")"

  users="$users,{\"name\":\"$name\",\"password\":\"$password\",\"tags\":[\"management\"]}"
  vhosts="$vhosts$separator{\"name\":\"$name\"}"
  # The tenant owns its vhost; the admin user sees every vhost from the UI.
  permissions="$permissions$separator{\"user\":\"$name\",\"vhost\":\"$name\",\"configure\":\".*\",\"write\":\".*\",\"read\":\".*\"}"
  permissions="$permissions,{\"user\":\"$admin\",\"vhost\":\"$name\",\"configure\":\".*\",\"write\":\".*\",\"read\":\".*\"}"
  separator=","
done

cat >"$DEFINITIONS_FILE" <<JSON
{
  "users": [$users],
  "vhosts": [$vhosts],
  "permissions": [$permissions],
  "topic_permissions": [],
  "policies": [],
  "parameters": [],
  "global_parameters": [],
  "exchanges": [],
  "queues": [],
  "bindings": []
}
JSON
# The rendered file holds plaintext passwords. The server drops privileges to the
# `rabbitmq` user before reading it, so keep it readable by that user only —
# root-owned 0600 fails validation with "load_definitions invalid ... cannot be
# read by the node".
chmod 0400 "$DEFINITIONS_FILE"
if [ "$(id -u)" = '0' ] && id rabbitmq >/dev/null 2>&1; then
  chown rabbitmq:rabbitmq "$DEFINITIONS_FILE"
fi

echo "lychen: provisioning vhosts [$TENANTS] (admin user '$ADMIN_USER')"

exec docker-entrypoint.sh "$@"
