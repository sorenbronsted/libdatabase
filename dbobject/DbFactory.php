<?php

class DbFactory {
  
  private static $connections = array();
  
  public static function getConnection($name) {
    if (isset(self::$connections[$name])) {
			return self::$connections[$name];
		}
		
		$config = DiContainer::instance()->config;
		if ($config == null) {
			throw new Exception("Config in DiContainer is not set");
		}
		foreach(array('driver','host','user','password') as $configName) {
			if ($config->{$name."_".$configName} == null) {
				throw new Exception("$configName in $name is not set");
			}
		}
		
		$connect = $config->{$name.'_driver'}.':host='.$config->{$name.'_host'}.';dbname='.$config->{$name.'_name'};
		$options = array();
		if ($config->{$name.'_driver'} == "mysql") {
			$options = array(
				PDO::ATTR_PERSISTENT => true,
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
			);
		}
		$pdo = new PDO($connect, $config->{$name.'_user'}, $config->{$name.'_password'}, $options);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		self::$connections[$name] = $pdo;
		return self::$connections[$name];
  }
}