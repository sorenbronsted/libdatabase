.PHONY:	dist clean test migrate generate install checkout coverage depend

SHELL=/bin/bash

all: checkout depend clean migrate coverage
	echo "Up-to-date"

dist:	
	bin/dist.sh

clean:
	rm -fr dist
	bin/clean.sh

test:
	phpunit test

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
	phpunit --coverage-html doc/coverage test

depend:
	bin/depend.sh install --dev
