prod: install

ready: dev
dev: prod
	@npm install

asset:
	@gulp default

watch:
	@gulp watch

install:
	@composer install --no-dev --optimize-autoloader

update:
	@composer update --no-dev --optimize-autoloader

test:
	@phpunit -c phpunit.xml
