<?php

spl_autoload_register(function($class) {
	$paths = array(
  "dbobject",
  "test/util",
	"vendor/ufds/libutil/config",
	"vendor/ufds/libutil/di",
	"vendor/ufds/libutil/log",
	"vendor/ufds/libtypes/types",
	);

	foreach($paths as $path) {
		$fullname = $path.'/'.$class.'.php';
		if (is_file($fullname)) {
			include($fullname);
			return true;
		}
	}
	return false;
});

date_default_timezone_set("Europe/Copenhagen");
error_reporting(E_ALL|E_STRICT);
openlog("libDb", LOG_PID | LOG_CONS, LOG_LOCAL0);

DiContainer::instance()->config = new Config2('test/util/config.ini');
DiContainer::instance()->log = Log::createFromConfig();

?>