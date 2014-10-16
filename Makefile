.PHONY:	dist clean test migrate generate install checkout coverage depend

SHELL=/bin/bash

all: checkout depend clean migrate coverage
	echo "Up-to-date"

dist:	
	bin/dist.sh

clean:
	rm -fr dist
	php bin/clean.php

test:
	bin/phpunit.phar test

migrate:
	bin/dbmigrate.sh db:migrate $(arg)

#
# usage: make generate arg=<ClassName>
#
generate:
	bin/generate.sh $(arg)

install:
	bin/install.sh

checkout:
	git pull

coverage:
	php bin/phpunit.phar --coverage-html doc/coverage test

depend:
	bin/depend.sh install

update-depend:
	bin/depend.sh update
