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
	composer exec --verbose phpunit tests -- --coverage-clover tests/coverage/clover.xml
