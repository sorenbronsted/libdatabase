
# This script expects to be execute from the root of the unpacked file

# Server config
CONFIG_FILE=/etc/ufds/hm-config.php
CONFIG_DEST_FILE=application/Config.php

if [ ! -f ${CONFIG_FILE} ]
then
  echo "${CONFIG_FILE} does not exists"
  echo "See serverConfigExample.php in doc directory"
  exit 1;
fi

if [ -f ${CONFIG_DEST_FILE} ]
then
  echo "${CONFIG_DEST_FILE} allready exists"
  exit 1;
fi

ln -s ${CONFIG_FILE} ${CONFIG_DEST_FILE}

# Client config
CONFIG_FILE=/etc/ufds/hm-config.js
CONFIG_DEST_FILE=public/control/Config.js

if [ ! -f ${CONFIG_FILE} ]
then
  echo "${CONFIG_FILE} does not exists"
  echo "See clientConfigExample.js in doc directory"
  exit 1;
fi

if [ -f ${CONFIG_DEST_FILE} ]
then
  echo "${CONFIG_DEST_FILE} allready exists"
  exit 1;
fi

ln -s ${CONFIG_FILE} ${CONFIG_DEST_FILE}

# Move application root
APP_ROOT=../ufds-hm
if [ -L ${APP_ROOT} ]
then
  rm -f $APP_ROOT
fi

ln -s `pwd` $APP_ROOT