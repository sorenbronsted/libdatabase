<?php

class DbFactory {
  
  private static $dbh = null;
  
  public static function getConnect() {
    if (self::$dbh == null) {
      $connect = Config::dbDriver.':host='.Config::dbHost.';dbname='.Config::dbName;
      $options = array();
      if (Config::dbDriver == "mysql") {
        $options = array(
          PDO::ATTR_PERSISTENT => true,
          PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        );
      }
      self::$dbh = new PDO($connect, Config::dbUser, Config::dbPassword, $options);
      self::$dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    }
    return self::$dbh;
  }
}