.PHONY:	create_db test migrate generate coverage depend update-depend

SHELL=/bin/bash

all: depend create_db migrate coverage
	@echo "Up-to-date"

create_db:
	mysql -uroot -proot < database/sql/dropdb.sql
	mysql -uroot -proot < database/sql/createdb.sql

test:
	vendor/bin/phpunit --testsuite "Test Suite"

migrate:
	vendor/bin/ruckus.php db:migrate

# usage: make generate arg=<ClassName>
generate:
	vendor/bin/ruckus.php db:generate $(arg)

coverage:
	vendor/bin/phpunit --testsuite "Test Suite" --coverage-html doc/coverage

depend:
	bin/composer.phar install --no-progress --no-suggest

update-depend:
	bin/composer.phar update --no-progress --no-suggest
