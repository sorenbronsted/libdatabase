#!/bin/sh
#set -x
if [ $# -lt 1 -o $# -gt 2 ]
then
  echo "Wrong number of arguments"
  echo "Usage $0 db:migrate [VERSION=<id>]"
  exit 1
fi

php -f vendor/ruckus/ruckusing-migrations/main.php $@
