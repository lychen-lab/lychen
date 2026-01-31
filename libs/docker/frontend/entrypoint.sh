#!/bin/sh

# Use envsubst to replace placeholders in the config.js file
# This plugin by default puts config.js in the root of the dist folder
CONFIG_FILE="/usr/share/nginx/html/config.js"

if [ -f "$CONFIG_FILE" ]; then
  echo "Replacing variables in $CONFIG_FILE..."
  # We use a temporary file to avoid 'empty file' issues when redirecting
  envsubst "$(printf '${%s} ' $(env | cut -d'=' -f1))" < "$CONFIG_FILE" > "$CONFIG_FILE.tmp" && mv "$CONFIG_FILE.tmp" "$CONFIG_FILE"
else
  echo "Warning: $CONFIG_FILE not found."
fi

# Execute the CMD from Dockerfile (usually nginx)
exec "$@"