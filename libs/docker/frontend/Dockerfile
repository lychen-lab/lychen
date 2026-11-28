FROM caddy:alpine

LABEL org.opencontainers.image.source=https://github.com/lychen-lab/lychen

# envsubst (gettext) is used by the entrypoint to inject runtime configuration
RUN apk add --no-cache gettext

COPY ./dist /usr/share/caddy/html
# The Caddyfile of the frontend's kind (the caddy-config build context:
# libs/caddy/pwa or libs/caddy/ssg, passed by docker-buildx.sh), then what the
# frontend adds to it (Caddyfile.d/) or its own Caddyfile, which replaces it. See
# libs/caddy/README.md. The bracket globs make both optional: a COPY whose
# sources match nothing copies nothing. The empty fragments spare Caddy a
# warning at every start for an import that matches no file.
RUN set -eux; \
	for point in global site extra; do \
		mkdir -p /etc/caddy/Caddyfile.d/$point; \
		touch /etc/caddy/Caddyfile.d/$point/.empty.Caddyfile; \
	done
COPY --from=caddy-config Caddyfile /etc/caddy/Caddyfile
COPY ./Caddyfile.[d] /etc/caddy/Caddyfile.d/
COPY ./Caddyfil[e] /etc/caddy/

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# The commit this image was built from, served as /version.txt: the staging deploy
# polls it to confirm the new version is the one actually answering.
ARG GIT_SHA=unknown
RUN echo "$GIT_SHA" > /usr/share/caddy/html/version.txt

ENTRYPOINT ["/entrypoint.sh"]

CMD ["caddy", "run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]
