#!/usr/bin/env bash

include .env
export $(shell sed 's/=.*//' .env)

DOCKER_COMPOSE = docker compose -p $(PROJECT_NAME)

CONTAINER_PHP := $(shell docker container ls -f "name=$(PROJECT_NAME)-php" -q)
CONTAINER_DB := $(shell docker container ls -f "name=$(PROJECT_NAME)-database" -q)
CONTAINER_QA := $(shell docker container ls -f "name=$(PROJECT_NAME)-php-qa" -q)

PHP := docker exec -ti $(CONTAINER_PHP)
DATABASE := docker exec -ti $(CONTAINER_DB)
QA := docker exec -ti $(CONTAINER_QA)

## Kill all containers
kill:
	@$(DOCKER_COMPOSE) kill $(CONTAINER) || true

## Build containers
build:
	@$(DOCKER_COMPOSE) build --pull --no-cache

## Init project
init: install update

## Start containers
start:
	@$(DOCKER_COMPOSE) up -d

## Stop containers
stop:
	@$(DOCKER_COMPOSE) down

restart: stop start

## Init project
init: install update npm fabric jwt db

npm: 
	$(PHP) npm install
	$(PHP) npm run build

cache:
	$(PHP) rm -r var/cache

## Entering php shell
php:
	@$(DOCKER_COMPOSE) exec php sh

## Entering database shell
database:
	@$(DOCKER_COMPOSE) exec database sh

## Composer install
install:
	$(PHP) composer install

## Composer update
update:
	$(PHP) composer update

fabric: 
	$(PHP) php bin/console messenger:setup-transports

jwt: 
	$(PHP) php bin/console lexik:jwt:generate-keypair --skip-if-exists

db: 
	$(PHP) php bin/console doctrine:database:drop -f
	$(PHP) php bin/console doctrine:database:create
	$(PHP) php bin/console doctrine:schema:update -f
	$(PHP) php bin/console hautelook:fixtures:load -n

fixtures:
	$(PHP) php bin/console hautelook:fixtures:load -n

php-cs-fixer:
	$(QA) ./php-cs-fixer fix src --rules=@Symfony --verbose --diff

php-stan:
	$(QA) ./vendor/bin/phpstan analyse src -l $(or $(level), 5)

php-rector:
	$(QA) ./vendor/bin/rector process src

php-rector-dry:
	$(QA) ./vendor/bin/rector process src --dry-run