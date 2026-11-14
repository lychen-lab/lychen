FROM caddy:alpine

LABEL org.opencontainers.image.source=https://github.com/lychen-lab/lychen

ARG CACHE_DATE="$(date)"

COPY ./storybook-static /usr/share/caddy/html
# The SSG frontends' Caddyfile, from the caddy-config build context:
# docker buildx build --build-context caddy-config=../../libs/caddy/ssg .
# Its imports (libs/caddy/README.md) get empty fragments: storybook adds nothing.
RUN set -eux; \
	for point in global site extra; do \
		mkdir -p /etc/caddy/Caddyfile.d/$point; \
		touch /etc/caddy/Caddyfile.d/$point/.empty.Caddyfile; \
	done
COPY --from=caddy-config Caddyfile /etc/caddy/Caddyfile

CMD ["caddy", "run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]