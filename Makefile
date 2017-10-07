# Default configuration for Mamp
DB_HOST := 127.0.0.1:8889
DB_NAME := affilicious_test
DB_USER := root
DB_PASSWORD := root
WP_VERSION := latest

prod:
	@composer install --no-dev --optimize-autoloader

ready: dev
dev: test-install
	@composer install
	@npm install

asset:
	@gulp default

watch:
	@gulp watch

install:
	@composer install

update:
	@composer update

test-install:
	@bin/install-wp-tests.sh $(DB_NAME) $(DB_USER) $(DB_PASSWORD) $(DB_HOST) $(WP_VERSION)

test-uninstall:
	@rm -rf tmp

test:
	@bin/phpunit
