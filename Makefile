cr:
	php bin/console cache:clear

init:
	composer install
	php bin/console doctrine:schema:drop --force
	php bin/console doctrine:schema:create
	php bin/console doctrine:fixtures:load --no-interaction -v

init-jwt:
	mkdir var/jwt
	openssl genrsa -out var/jwt/private.pem -aes256 4096
	openssl rsa -pubout -in var/jwt/private.pem -out var/jwt/public.pem

serve:
	php bin/console server:run

test-init:
	-php bin/console doctrine:database:drop --force --env=test
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:schema:create --env=test
	php bin/console doctrine:fixtures:load --no-interaction --env=test

test-full: test-init
	./vendor/bin/simple-phpunit test/

test:
	./vendor/bin/simple-phpunit --filter 'ProjectFormTypeTest'

assets:
	php bin/console assets:install --env=dev

assets-prod:
	php bin/console assets:install --env=prod

websocket:
	php bin/console gos:websocket:server
