install:
	composer install

validate:
	composer validate

gendiff:
	./bin/gendiff

lint:
	composer exec phpcs -- --standard=PSR12 src bin

test:
	composer exec --verbose phpunit tests

test-coverage:
	composer exec --verbose XDEBUG_MODE=coverage phpunit tests -- --coverage-clover build/logs/clover.xml
