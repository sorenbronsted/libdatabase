.PHONY:	clean test checkout coverage

SHELL=/bin/bash

all:
	echo "Up-to-date"

clean:
	bin/clean.sh

test:
	phpunit test

checkout:
	git pull

coverage:
	phpunit --coverage-html doc/coverage test
