.PHONY all:
all: test

.PHONY test:
test:
	php vendor/bin/phpunit --display-warnings --display-notices --display-deprecations

.PHONY coverage:
coverage:
	php -d xdebug.mode=coverage vendor/bin/phpunit --coverage-html=build/coverage --coverage-clover=build/coverage.xml  --display-warnings --display-notices --display-deprecations
