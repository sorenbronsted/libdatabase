<?php

require_once "test/util/Config.php";

//----------------------------
// DATABASE CONFIGURATION
//----------------------------
return array(
  'db' => array(
      'development' => array(
      'type'      => Config::dbDriver,
      'host'      => Config::dbHost,
      'port'      => 3306,
      'database'  => Config::dbName,
      'user'      => Config::dbUser,
      'password'  => Config::dbPassword
    ),
  ),
  'migrations_dir' => RUCKUSING_WORKING_BASE . '/database/migrations'
);

?>