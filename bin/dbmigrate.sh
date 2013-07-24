#!/bin/bash
#set -x

if [ $# -ge 2 ]
then
  echo "Wrong number of arguments"
  echo "Usage $0 [VERSION=<id>]"
  exit 1
fi

php -f vendor/bin/ruckus.php db:migrate $1
