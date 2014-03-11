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
		if ($config->{$name."_driver"} == null) {
			throw new Exception("driver in $name is not set");
		}
		
		$dsn = self::dsn($name, $config);
		$options[PDO::ATTR_PERSISTENT] = true;
		$pdo = new PDO($dsn, $config->{$name.'_user'}, $config->{$name.'_password'}, $options);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		self::$connections[$name] = $pdo;
		return self::$connections[$name];
  }
	
	private static function dsn($name, $config) {
		switch ($config->{$name.'_driver'}) {
			case 'mysql':
				return $config->{$name.'_driver'}.
					':host='.$config->{$name.'_host'}.
					';dbname='.$config->{$name.'_name'}.
					';charset='.$config->{$name.'_charset'};
			case 'sqlite':
				return $config->{$name.'_driver'}.
					':'.$config->{$name.'_name'};
		}
	}
}