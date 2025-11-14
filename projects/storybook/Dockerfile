FROM nginx:alpine

LABEL org.opencontainers.image.source=https://github.com/lychen-lab/lychen

ARG CACHE_DATE="$(date)"

COPY ./storybook-static /usr/share/nginx/html
COPY ./.nginx-build/default.conf /etc/nginx/conf.d/default.conf