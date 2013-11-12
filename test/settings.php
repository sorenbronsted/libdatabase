<?php
date_default_timezone_set("Europe/Copenhagen");
error_reporting(E_ALL|E_STRICT);
openlog("libDb", LOG_PID | LOG_CONS, LOG_LOCAL0);

$paths = array(
  "dbobject",
  "types",
  "test/util",
);

set_include_path(get_include_path().":".implode(':', $paths));

spl_autoload_register(function($class) {
  require("$class.php");
});

?>