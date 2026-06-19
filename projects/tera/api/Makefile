# TODO Needs to be sort to have only moon task as reference
# Executables (local)
DOCKER_COMP = docker compose
DOCKER_COMP_CI = docker compose -f compose.yml

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php
PHP_CONT_CI = $(DOCKER_COMP_CI) run --rm php
PHP_CONT_RUN = $(DOCKER_COMP) run --rm php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc test

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) sh

bash: ## Connect to the FrankenPHP container via bash so up and down arrows go to previous commands
	@$(PHP_CONT) bash

test: ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c)

## —— Docker CI 🐳 ————————————————————————————————————————————————————————————————
ci-kill:
	@$(DOCKER_COMP_CI) kill
	@$(DOCKER_COMP_CI) rm -v -f

ci-up-php:
	@$(DOCKER_COMP_CI) up -d --remove-orphans php

ci-up-db-php:
	@$(DOCKER_COMP_CI) up -d --remove-orphans php database

ci-up:
	@$(DOCKER_COMP_CI) up -d --remove-orphans redis database mailcatcher

ci-install-tools:
	@$(PHP_CONT_CI) composer install --no-interaction --working-dir=tools/

ci-php-cs-fixer:
	@$(PHP_CONT_CI) tools/vendor/bin/php-cs-fixer fix src/ --rules=@Symfony
	@$(PHP_CONT_CI) tools/vendor/bin/php-cs-fixer fix tests/ --rules=@Symfony

ci-validate-db-schema:
	#@$(PHP_CONT_CI) bin/console doctrine:database:create -n --if-not-exists
	#@$(PHP_CONT_CI) bin/console doctrine:schema:update --force
	@$(PHP_CONT_CI) bin/console doctrine:schema:validate -n

ci-check-known-vulnerabilities:
	@$(PHP_CONT_CI) ./local-php-security-checker

ci-composer-install:
	@$(PHP_CONT_CI) composer install --no-interaction

ci-phpunit:
	@$(PHP_CONT_CI) php -d memory_limit=-1 ./vendor/bin/phpunit ${FOLDER}

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

## —— Symfony 🎵 ——————————————————————————————————————————————————————————————
##

load-fixtures: ## Load fixtures for dev environment and ensure that RabbitMQ messages are deleted
	@$(DOCKER_COMP) exec -it php sh -c "php -d memory_limit=-1 bin/console do:fi:lo --no-interaction"
	curl -XDELETE -u guest:guest http://rabbitmq:15672/api/queues/tera/async/contents

phpunit:
	@$(PHP_CONT) php -d memory_limit=-1 ./vendor/bin/phpunit ${FOLDER}
