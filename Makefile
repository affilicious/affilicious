prod:
	@composer install --no-dev
	make clean

dev:
	@composer install
	@npm install --only=dev

install:
	@composer install

update:
	@composer update

npm:
	@npm install --only=dev

clean:
	@rm -rf vendor/
	@rm -rf tmp/
	@rm -rf node_modules/
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
