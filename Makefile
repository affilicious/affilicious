install:
	@composer install --no-dev

update:
	@composer update

clean:
	@rm -rf vendor/
	@rm -rf tmp/
	@composer install --no-dev

test-install-mamp:
	if [[ ! -d "vendor/phpunit" ]]; then composer install --dev; fi
	@./tests/install.sh affilicious-plugin-test root root 127.0.0.1:8889

test:
	if [[ ! -d "vendor/phpunit" ]]; then composer install; fi
	@phpunit

test-uninstall:
	@rm -rf vendor/
	@rm -rf tmp/
	@composer install --no-dev
