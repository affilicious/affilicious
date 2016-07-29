install:
	@composer install

update:
	@composer update

clean:
	@rm -rf vendor/
	@rm -rf tmp/

test-install-mamp:
	if [[ ! -d "vendor" ]]; then composer install; fi
	@./tests/install.sh affilicious-products-plugin-test root root 127.0.0.1:8889

test:
	@phpunit

test-uninstall:
	@rm -rf tmp/
