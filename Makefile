.PHONY all:
all: test

.PHONY test:
test:
	php vendor/bin/phpunit

.PHONY coverage:
coverage:
	php -d xdebug.mode=coverage vendor/bin/phpunit --coverage-html=build/coverage
