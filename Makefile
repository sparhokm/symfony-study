init: docker-down-clear \
	manager-api-clear \
	docker-pull docker-build docker-up \
	manager-api-init
up: docker-up
down: docker-down
restart: down up

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build --pull

manager-api-clear:
	docker run --rm -v ${PWD}/manager:/app -w /app alpine sh -c 'rm -rf var/cache/* var/log/* var/test/*'

manager-api-wait-db:
	docker-compose run --rm manager-api-php-cli wait-for-it manager-postgres:5432 -t 30

manager-api-migrations:
	docker-compose run --rm manager-api-php-cli php bin/console doctrine:migrations:migrate --no-interaction

manager-api-init: manager-api-composer-install manager-api-wait-db manager-api-migrations

manager-api-composer-install:
	docker-compose run --rm manager-api-php-cli composer install

manager-api-composer-update:
	docker-compose run --rm manager-api-php-cli composer update

manager-api-check: manager-api-lint manager-api-analyze

manager-api-lint:
	docker-compose run --rm manager-api-php-cli composer lint
	docker-compose run --rm managerr-api-php-cli composer php-cs-fixer fix -- --dry-run --diff

manager-api-cs-fix:
	docker-compose run --rm managerr-api-php-cli composer php-cs-fixer fix

manager-api-analyze:
	docker-compose run --rm managerr-api-php-cli composer psalm -- --no-diff

manager-api-analyze-diff:
	docker-compose run --rm managerr-api-php-cli composer psalm
