FROM caddy:alpine

LABEL org.opencontainers.image.source=https://github.com/lychen-lab/lychen

ARG CACHE_DATE="$(date)"

COPY ./storybook-static /usr/share/caddy/html
COPY ./.caddy-build/Caddyfile /etc/caddy/Caddyfile

CMD ["caddy", "run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]