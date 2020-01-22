<?php
require_once 'test/settings.php';

//----------------------------
// DATABASE CONFIGURATION
//----------------------------
return array(
  'db' => array(
      'development' => array(
        'type'      => $dic->config->mysql_driver,
        'host'      => $dic->config->mysql_host,
        'port'      => $dic->config->mysql_port,
        'database'  => $dic->config->mysql_name,
        'user'      => $dic->config->mysql_user,
        'password'  => $dic->config->mysql_password,
        'charset'   => $dic->config->mysql_charset
      ),
    ),
  'ruckusing_base' => __DIR__.'/vendor/ruckusing/ruckusing-migrations',
  'migrations_dir' => RUCKUSING_WORKING_BASE . '/database',
  'db_dir' => RUCKUSING_WORKING_BASE . '/db',
  'log_dir' => '/tmp/logs',
);

?>
