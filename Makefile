all: oc phpcs dunit phpunit

init:
	if [ ! -d vendor ]; then composer install; fi;

oc: init
	./vendor/bin/phpcs --standard=phpcs.xml src/

phpcs: init
	./vendor/bin/phpcs --standard=PSR2 src/

phpunit: init
	./vendor/bin/phpunit --coverage-text --coverage-html covHtml

dunit: init
	./vendor/bin/dunit
