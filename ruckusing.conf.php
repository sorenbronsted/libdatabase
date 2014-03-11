<?php

require_once "test/settings.php";

$config = DiContainer::instance()->config;

//----------------------------
// DATABASE CONFIGURATION
//----------------------------
return array(
  'db' => array(
      'development' => array(
      'type'      => $config->defaultDb_driver,
      'host'      => $config->defaultDb_host,
      'port'      => $config->defaultDb_post,
      'database'  => $config->defaultDb_name,
      'user'      => $config->defaultDb_user,
      'password'  => $config->defaultDb_password,
      'charset'   => $config->defaultDb_charset,
    ),
  ),
  'migrations_dir' => RUCKUSING_WORKING_BASE . '/database',
  'db_dir' => RUCKUSING_WORKING_BASE . '/db',
  'log_dir' => RUCKUSING_WORKING_BASE . '/logs',
  'ruckusing_base' => dirname(__FILE__) . '/vendor/ruckusing/ruckusing-migrations'
);

?>