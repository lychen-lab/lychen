#!/bin/bash
echo '> Deploy through Dokploy API'

curl -X POST "${DOKPLOY_API_URL}/compose.deploy" \
  -H "accept: application/json" \
  -H "Content-Type: application/json" \
  -H "x-api-key: ${DOKPLOY_API_TOKEN}" \
  -d "{\"composeId\": \"${DOKPLOY_COMPOSE_ID}\"" || exit 1

echo '> Deployed'
