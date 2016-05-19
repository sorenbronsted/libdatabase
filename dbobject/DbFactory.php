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
			throw new Exception("driver with $name is not found");
		}
		
		$dsn = self::dsn($name, $config);
		if (empty($dsn)) {
			throw new ConnectionException('dsn is empty', __FILE__, __LINE__);
		}
	  DiContainer::instance()->log->debug(__CLASS__, "dsn: $dsn");
		$pdo = new PDO($dsn, $config->{$name.'_user'}, $config->{$name.'_password'});
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		if ($config->{$name.'_schema'} != null) {
			$pdo->exec('set schema '.$config->{$name.'_schema'});
		}
		self::$connections[$name] = $pdo;
		return self::$connections[$name];
  }
	
	private static function dsn($name, $config) {
		$result = null;
		switch ($config->{$name.'_driver'}) {
			case 'mysql':
				$result = $config->{$name.'_driver'}.
					':host='.$config->{$name.'_host'}.
					';dbname='.$config->{$name.'_name'}.
					';charset='.$config->{$name.'_charset'};
				break;
			case 'sqlite':
				$result = $config->{$name.'_driver'}.
					':'.$config->{$name.'_name'};
				break;
			case 'ibm':
				$result = 'ibm:driver={ibm db2 odbc driver}'.
					';database='.$config->{$name.'_name'}.
					';hostname='.$config->{$name.'_host'}.
					';port='.$config->{$name.'_port'}.
				  ';protocol=tcpip';
				break;
		}
		return $result;
	}
}
