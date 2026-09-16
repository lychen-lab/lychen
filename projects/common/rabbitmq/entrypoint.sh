#!/bin/sh
# Render the RabbitMQ definitions file from the environment, then hand over to
# the image's own entrypoint.
#
# Topology — one vhost, one topic exchange, one queue per service:
#
#   exchange lychen.events (topic, durable)          <- every service publishes here
#     |-- tera.#   --> queue tera.events   (quorum, DLX)
#     |-- espace.# --> queue espace.events (quorum, DLX)
#     |-- flora.#  --> queue flora.events  (quorum, DLX)
#
#   exchange lychen.events.dlx (topic, durable)      <- definitive failures
#     |-- tera.events.dlq --> queue tera.events.dlq  (quorum)
#     |-- ...                  one .dlq per service
#
#   exchange lychen.events.delays (direct, durable)  <- Symfony Messenger retries
#
# Routing keys follow `<domain>.<aggregate>.<action>.v<n>`, e.g.
# `tera.garden.created.v1`. A service listens to its own domain by default
# (`<service>.#`); subscribing to another domain's events is a matter of adding
# its routing keys to RABBITMQ_<SERVICE>_BINDING_KEYS.
#
# Each queue also gets one binding on its own name (`tera.events` -> queue
# `tera.events`). That one is not for domain events: it is where Messenger's
# delay queues put a message back once its retry delay has expired, addressed to
# a single queue rather than broadcast. It is what lets the APIs create those
# delay queues with nothing beyond their own permissions — see the `delay`
# options in each API's config/packages/messenger.php.
#
# There is no vhost per service any more: isolation comes from per-service users
# whose permissions are scoped to their own queues, plus topic permissions that
# scope each service to publishing under its own routing-key namespace.
#
# Why render this instead of committing a definitions.json: a definitions file
# cannot interpolate environment variables, and RABBITMQ_DEFAULT_USER/PASS/VHOST
# are no alternative — they only ever seed a *single* vhost, and the node skips
# them outright as soon as it has definitions to import ("Will not seed default
# virtual host and user: have definitions to load..."). Rendering at boot is what
# lets this one compose provision dev, staging and production, each with its own
# credentials.
#
# Definitions are re-imported on every boot and are idempotent: vhosts, users,
# exchanges and bindings that already exist are updated in place, and queues and
# their messages are untouched. Queue *arguments* are the exception — RabbitMQ
# cannot change them on an existing queue, so changing one below means deleting
# the queue (or renaming it) before the new definition takes effect.
set -eu

VHOST="${RABBITMQ_VHOST:-lychen}"
EXCHANGE="${RABBITMQ_EXCHANGE:-lychen.events}"
SERVICES="${RABBITMQ_SERVICES:-tera espace flora}"
ADMIN_USER="${RABBITMQ_ADMIN_USER:-admin}"
ADMIN_PASSWORD="${RABBITMQ_ADMIN_PASSWORD:-admin}"
# Redeliveries a message gets before RabbitMQ dead-letters it, which also caps
# poison-message loops. Symfony's own retry strategy sits in front of this.
DELIVERY_LIMIT=5
DEFINITIONS_FILE="${RABBITMQ_DEFINITIONS_FILE:-/etc/rabbitmq/definitions.json}"

DLX="$EXCHANGE.dlx"
DELAY_EXCHANGE="$EXCHANGE.delays"

# JSON-escape a value, so a password containing " or \ cannot break the document.
json_escape() {
  printf '%s' "$1" | sed -e 's/\\/\\\\/g' -e 's/"/\\"/g'
}

# Turn a name into a regex matching it literally. `.` becomes `[.]` rather than
# `\.` so the result needs no second round of escaping once embedded in JSON.
regex_escape() {
  printf '%s' "$1" | sed -e 's/\./[.]/g'
}

# service_password <service> — $RABBITMQ_<SERVICE>_PASSWORD, falling back to the
# service name so a fresh checkout boots with no configuration at all.
service_password() {
  _var="RABBITMQ_$(printf '%s' "$1" | tr 'a-z-' 'A-Z_')_PASSWORD"
  eval "_value=\${$_var:-}"
  [ -n "$_value" ] || _value="$1"
  printf '%s' "$_value"
}

# service_binding_keys <service> — space-separated routing keys the service's
# queue subscribes to. Defaults to its own domain.
service_binding_keys() {
  _var="RABBITMQ_$(printf '%s' "$1" | tr 'a-z-' 'A-Z_')_BINDING_KEYS"
  eval "_value=\${$_var:-}"
  [ -n "$_value" ] || _value="$1.#"
  printf '%s' "$_value"
}

vhost="$(json_escape "$VHOST")"
exchange="$(json_escape "$EXCHANGE")"
dlx="$(json_escape "$DLX")"
delay_exchange="$(json_escape "$DELAY_EXCHANGE")"
admin="$(json_escape "$ADMIN_USER")"

exchange_re="$(regex_escape "$EXCHANGE")"
delay_exchange_re="$(regex_escape "$DELAY_EXCHANGE")"

users="{\"name\":\"$admin\",\"password\":\"$(json_escape "$ADMIN_PASSWORD")\",\"tags\":[\"administrator\"]}"
# The admin user sees everything from the management UI.
permissions="{\"user\":\"$admin\",\"vhost\":\"$vhost\",\"configure\":\".*\",\"write\":\".*\",\"read\":\".*\"}"
topic_permissions=""
queues=""
bindings=""
topic_separator=""
queue_separator=""
binding_separator=""

for service in $SERVICES; do
  name="$(json_escape "$service")"
  password="$(json_escape "$(service_password "$service")")"
  queue="$name.events"
  dlq="$queue.dlq"
  service_re="$(regex_escape "$service")"
  # `<service>.events`, `<service>.events.dlq` and the `<service>.events.delay.*`
  # queues Messenger names on the fly.
  own_queues="$service_re"'[.]events([.].+)?'
  # Of those, the only ones an API is allowed to declare itself.
  own_delay_queues="$service_re"'[.]events[.]delay[.].+'

  users="$users,{\"name\":\"$name\",\"password\":\"$password\",\"tags\":[\"management\"]}"

  # A service declares nothing durable — the topology below is authoritative and
  # the APIs run with `auto_setup: false`. The one thing it may configure is the
  # delay queues Messenger names on the fly. It publishes to the bus and to the
  # delay exchange, and reads only its own queues.
  permissions="$permissions,{\"user\":\"$name\",\"vhost\":\"$vhost\",\"configure\":\"^$own_delay_queues\$\",\"write\":\"^($exchange_re|$delay_exchange_re|$own_delay_queues)\$\",\"read\":\"^($delay_exchange_re|$own_queues)\$\"}"
  # Publishing is confined to the service's own routing-key namespace, which is
  # what keeps one service from forging another's events on the shared exchange.
  topic_permissions="$topic_permissions$topic_separator{\"user\":\"$name\",\"vhost\":\"$vhost\",\"exchange\":\"$exchange\",\"write\":\"^${service_re}[.]\",\"read\":\".*\"}"

  queues="$queues$queue_separator{\"name\":\"$queue\",\"vhost\":\"$vhost\",\"durable\":true,\"auto_delete\":false,\"arguments\":{\"x-queue-type\":\"quorum\",\"x-dead-letter-exchange\":\"$dlx\",\"x-dead-letter-routing-key\":\"$dlq\",\"x-dead-letter-strategy\":\"at-least-once\",\"x-overflow\":\"reject-publish\",\"x-delivery-limit\":$DELIVERY_LIMIT}}"
  # No DLX on the dead-letter queue itself, so a failure cannot loop.
  queues="$queues,{\"name\":\"$dlq\",\"vhost\":\"$vhost\",\"durable\":true,\"auto_delete\":false,\"arguments\":{\"x-queue-type\":\"quorum\"}}"

  for binding_key in $(service_binding_keys "$service"); do
    bindings="$bindings$binding_separator{\"source\":\"$exchange\",\"vhost\":\"$vhost\",\"destination\":\"$queue\",\"destination_type\":\"queue\",\"routing_key\":\"$(json_escape "$binding_key")\",\"arguments\":{}}"
    binding_separator=","
  done
  # Where an expired Messenger retry goes back in — see the note at the top.
  bindings="$bindings$binding_separator{\"source\":\"$exchange\",\"vhost\":\"$vhost\",\"destination\":\"$queue\",\"destination_type\":\"queue\",\"routing_key\":\"$queue\",\"arguments\":{}}"
  bindings="$bindings$binding_separator{\"source\":\"$dlx\",\"vhost\":\"$vhost\",\"destination\":\"$dlq\",\"destination_type\":\"queue\",\"routing_key\":\"$dlq\",\"arguments\":{}}"

  topic_separator=","
  queue_separator=","
  binding_separator=","
done

cat >"$DEFINITIONS_FILE" <<JSON
{
  "users": [$users],
  "vhosts": [{"name":"$vhost"}],
  "permissions": [$permissions],
  "topic_permissions": [$topic_permissions],
  "policies": [],
  "parameters": [],
  "global_parameters": [],
  "exchanges": [
    {"name":"$exchange","vhost":"$vhost","type":"topic","durable":true,"auto_delete":false,"internal":false,"arguments":{}},
    {"name":"$dlx","vhost":"$vhost","type":"topic","durable":true,"auto_delete":false,"internal":false,"arguments":{}},
    {"name":"$delay_exchange","vhost":"$vhost","type":"direct","durable":true,"auto_delete":false,"internal":false,"arguments":{}}
  ],
  "queues": [$queues],
  "bindings": [$bindings]
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

echo "lychen: provisioning vhost '$VHOST', exchange '$EXCHANGE' and queues for [$SERVICES] (admin user '$ADMIN_USER')"

exec docker-entrypoint.sh "$@"
