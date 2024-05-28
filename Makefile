build: ## rebuild docker container
	@docker-compose up -d --build > /dev/null

composer-install: ## run composer installation within the docker containers (useful for local development)
	@docker-compose run --rm php_package_1 composer install

run:  ## run container
	@docker-compose up -d > /dev/null

attach: ## Entrance to the container in an interactive shell
	@docker exec -it php_package_1 bash

shutdown:
	@docker-compose down

test: ## run tests
	@docker-compose run --rm php_package_1 composer phpunit

static-analysis-phpstan: ## verify code type-level soundness
	docker-compose run --rm php_package_1 composer phpstan

static-analysis-psalm: ## verify code type-level soundness
	docker-compose run --rm php_package_1 composer psalm
