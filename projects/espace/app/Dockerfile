FROM nginx:alpine

LABEL org.opencontainers.image.source=https://github.com/lychen-lab/lychen

COPY ./dist /usr/share/nginx/html
COPY ./.nginx-build/default.conf /etc/nginx/conf.d/default.conf
COPY ./dist/index.html /usr/share/nginx/html/index.template.html

CMD ["sh", "-c", "envsubst < /usr/share/nginx/html/index.template.html > /usr/share/nginx/html/index.html && nginx -g 'daemon off;'"]