APP=symfony

psalm:
	docker exec $(APP) php ./vendor/bin/psalm --no-cache

run_unit_test:
	docker exec $(APP) php vendor/bin/phpunit --testdox

coverage:
	docker exec $(APP) env XDEBUG_MODE=coverage php vendor/bin/phpunit --coverage-html var/coverage
	google-chrome ./var/coverage/index.html

ecs_fix:
	docker exec $(APP) php ./vendor/bin/ecs --fix

ecs:
	docker exec $(APP) php ./vendor/bin/ecs

build:
	@docker exec $(APP) git config --global --add safe.directory /var/www
	@docker exec $(APP) composer install
	@docker exec $(APP) bin/console doctrine:migrations:migrate --no-interaction

update:
	@composer install
	@bin/console doctrine:migrations:migrate --no-interaction
	@bin/console cache:clear

worker:
	supervisord -c /var/www/html/worker.conf -s

psalm:
	@vendor/bin/psalm --no-cache

composer-install:
	@composer install --optimize-autoloader

ecs:
	@vendor/bin/ecs

ecs-fix:
	@vendor/bin/ecs --fix