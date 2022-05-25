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

#phpstan:
#	vendor/bin/phpstan analyse src tests
