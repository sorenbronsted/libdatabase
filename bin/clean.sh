
#!/bin/sh
#set -v

CONFIG=test/util/Config.php

if [ ! -f ${CONFIG} ]
then
  echo "${CONFIG} is not found"
  exit 1;
fi

USER=`grep dbUser ${CONFIG} | cut -d, -f2 | cut -d\' -f2`
PASSWORD=`grep dbPassword ${CONFIG} | cut -d, -f2 | cut -d\' -f2`
HOST=`grep dbHost ${CONFIG} | cut -d, -f2 | cut -d\' -f2`
DB=`grep dbName ${CONFIG} | cut -d, -f2 | cut -d\' -f2`

if [ -n $HOST -a -n $USER -a -n $PASSWORD -a -n $DB ]
then
  mysql --host=$HOST --user=$USER --password=$PASSWORD $DB < database/sql/clean.sql
else
  echo "HOST, USER, PASSWORD and DB is not defined"
  exit 3;
fi

