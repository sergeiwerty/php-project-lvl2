install:
	composer install

validate:
	composer validate

gendiff:
	./bin/gendiff

lint:
	composer exec phpcs -- --standard=PSR12 src bin

#phpstan:
#	vendor/bin/phpstan analyse src tests
