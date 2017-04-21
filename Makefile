DB_HOST := 127.0.0.1:8889
DB_NAME := affilicious_test
DB_USER := root
DB_PASSWORD := root
WP_VERSION := latest

prod:
	@composer install --no-dev --optimize-autoloader

ready: dev
dev: tests-install
	@composer install
	@npm install

wordpress: prod clean

asset:
	@gulp default

watch:
	@gulp watch

install:
	@composer install

update:
	@composer update

clean:
	@rm -rf assets/.cache
	@rm -rf assets/.sass-cache
	@rm -rf assets/*/*.map
	@rm -rf tmp

tests-database:
	@bin/install-tests.sh $(DB_NAME) $(DB_USER) $(DB_PASSWORD) $(DB_HOST)

tests-install:
	@composer install
	@bin/install-tests.sh $(DB_NAME) $(DB_USER) $(DB_PASSWORD) $(DB_HOST) $(WP_VERSION) true

tests:
	@phpunit

tests-uninstall:
	@composer install --no-dev

selenium:
	@selenium-server -port 4444

behat:
	@bin/behat

server:
	@php -S localhost:8000 -t tmp/wordpress -d disable_functions=mail
