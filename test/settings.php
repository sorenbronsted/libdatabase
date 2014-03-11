<?php
date_default_timezone_set("Europe/Copenhagen");
error_reporting(E_ALL|E_STRICT);
openlog("libDb", LOG_PID | LOG_CONS, LOG_LOCAL0);

$paths = array(
  "dbobject",
  "types",
  "test/util",
	"vendor/ufds/libutil/config",
	"vendor/ufds/libutil/di",
	"vendor/ufds/libutil/log",
);

set_include_path(get_include_path().":".implode(':', $paths));

spl_autoload_register(function($class) {
  require("$class.php");
});

DiContainer::instance()->config = new Config2('test/util/config.ini');
DiContainer::instance()->log = new Log();

?>