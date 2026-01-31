FROM nginx:alpine

LABEL org.opencontainers.image.source=https://github.com/lychen-lab/lychen

COPY ./dist /usr/share/nginx/html
COPY ./.nginx-build/default.conf /etc/nginx/conf.d/default.conf
COPY ./dist/config.js /usr/share/nginx/html/config.js

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

CMD ["nginx", "-g", "daemon off;"]