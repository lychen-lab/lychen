FROM caddy:alpine

LABEL org.opencontainers.image.source=https://github.com/lychen-lab/lychen

# envsubst (gettext) is used by the entrypoint to inject runtime configuration
RUN apk add --no-cache gettext

COPY ./dist /usr/share/caddy/html
COPY ./.caddy-build/Caddyfile /etc/caddy/Caddyfile

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

CMD ["caddy", "run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]
