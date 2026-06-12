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

# Replace ${HOST} in SSG HTML files (canonical tags baked at build time)
echo "Replacing variables in HTML files..."
find /usr/share/nginx/html -name "*.html" | while read -r file; do
  envsubst '${HOST}' < "$file" > "$file.tmp" && mv "$file.tmp" "$file"
done

# Replace ${HOST} in sitemap.xml. vite-ssg-sitemap passes the hostname through
# new URL(), which lowercases it, so the baked placeholder ends up as ${host}.
SITEMAP_FILE="/usr/share/nginx/html/sitemap.xml"
if [ -f "$SITEMAP_FILE" ]; then
  echo "Replacing variables in $SITEMAP_FILE..."
  sed -e "s|\${HOST}|${HOST}|g" -e "s|\${host}|${HOST}|g" "$SITEMAP_FILE" > "$SITEMAP_FILE.tmp" && mv "$SITEMAP_FILE.tmp" "$SITEMAP_FILE"
else
  echo "Warning: $SITEMAP_FILE not found."
fi

# Execute the CMD from Dockerfile (usually nginx)
exec "$@"