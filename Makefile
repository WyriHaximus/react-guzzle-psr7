all: oc cs dunit unit
travis: cs unit-travis
contrib: cs dunit unit

init:
	if [ ! -d vendor ]; then composer install; fi;

oc: init
	./vendor/bin/phpcs --standard=phpcs.xml src/

cs: init
	./vendor/bin/phpcs --standard=PSR2 src/

unit: init
	./vendor/bin/phpunit --coverage-text --coverage-html covHtml

unit-travis: init
	./vendor/bin/phpunit --coverage-text --coverage-clover ./build/logs/clover.xml

dunit: init
	./vendor/bin/dunit

dunit-nightly: init
	./vendor/bin/dunit -c .dunitconfig-nightly
