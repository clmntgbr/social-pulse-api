#!/usr/bin/env bash

down:
	docker compose down --remove-orphans

up:
	docker compose up -d

php:
	docker exec -it social-pulse-api-php /bin/bash

build:
	docker compose build --no-cache