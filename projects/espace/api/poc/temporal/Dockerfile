# Minimal PHP + RoadRunner image hosting the Temporal worker.
FROM php:8.4-cli-alpine

RUN apk add --no-cache $PHPIZE_DEPS linux-headers protobuf-dev \
    && pecl install grpc protobuf \
    && docker-php-ext-enable grpc protobuf \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY composer.json ./
RUN composer install --no-interaction --no-dev --prefer-dist \
    && vendor/bin/rr get-binary --location /usr/local/bin

COPY . .

CMD ["rr", "serve", "-c", ".rr.yml"]
