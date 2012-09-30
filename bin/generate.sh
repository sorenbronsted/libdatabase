#!/bin/sh
if [ $# -ne 1 ]
then
  echo "Wrong number of arguments"
  echo "Usage $0 <name-of-migrations-class>"
  exit 1
fi
php -f vendor/ruckus/ruckusing-migrations/generate.php $1
