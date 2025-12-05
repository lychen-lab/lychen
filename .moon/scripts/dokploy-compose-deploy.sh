#!/bin/bash

while getopts i: flag
do
    case "${flag}" in
        i) composeId=${OPTARG};;
    esac
done

if [ -z "$composeId" ]; then
    echo "Error: -i (composeId) is required" >&2
    exit 1
fi

echo '> Deploy through Dokploy API'

curl -X POST "${DOKPLOY_API_URL}/compose.deploy" \
  -H "accept: application/json" \
  -H "Content-Type: application/json" \
  -H "x-api-key: ${DOKPLOY_API_TOKEN}" \
  -d "{\"composeId\": \"${DOKPLOY_COMPOSE_ID}\"" || exit 1

echo '> Deployed'
