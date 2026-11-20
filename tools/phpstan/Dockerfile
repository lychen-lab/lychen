# syntax=docker/dockerfile:1
# The monorepo's PHPStan, on its own: the official PHP image with the tools of
# composer.json (PHPStan, its Symfony, Doctrine and PHPUnit extensions, Rector)
# installed in it. .moon/scripts/phpstan.sh builds it on first use, again when
# this file or composer.lock changes, and runs it with the repository mounted:
# nothing on the host but Docker, as for php-cs-fixer. It shares nothing with
# the APIs' image (libs/docker/symfony/Dockerfile): only the PHP minor has to be
# theirs, so that PHPStan reads the code as they run it. The patch follows the
# official image. PHPStan needs none of the APIs' extensions: it reads their
# stubs.
FROM php:8.5-cli-alpine

WORKDIR /opt/phpstan
COPY composer.json composer.lock ./
# Composer only installs the tools: it is mounted for this step, not kept, and
# leaves no cache. The install runs as root, which Composer's plugins (here
# phpstan/extension-installer) only accept with COMPOSER_ALLOW_SUPERUSER.
# memory_limit: PHPStan is the only thing that runs here.
RUN --mount=from=composer:2.11.2,source=/usr/bin/composer,target=/usr/local/bin/composer \
    apk add --no-cache --virtual .install unzip \
 && COMPOSER_ALLOW_SUPERUSER=1 COMPOSER_HOME=/tmp/composer \
    composer install --no-interaction --no-progress --no-cache \
 && rm -rf /tmp/composer \
 && apk del .install \
 && echo 'memory_limit = -1' > "$PHP_INI_DIR/conf.d/phpstan.ini"

# Takes phpstan's own arguments (analyse, diagnose…). The repository is mounted
# by the runner, and the working directory set to the analysed project.
ENTRYPOINT ["php", "/opt/phpstan/vendor/bin/phpstan"]
