install:
	@composer install

update:
	@composer update

clean:
	@rm -rf vendor/
	@rm -rf tmp/

test-install-mamp:
	@composer install
	@./bin/install-wp-tests.sh affilicious-products-plugin-test root root 127.0.0.1:8889

test:
	if [[ ! -d "vendor" ]]; then composer install; fi
	if [[ ! -d "tmp" ]]; then ./bin/install-wp-tests.sh affilicious-products-plugin-test root root 127.0.0.1; fi
	@phpunit

test-uninstall:
	@rm -rf tmp/
