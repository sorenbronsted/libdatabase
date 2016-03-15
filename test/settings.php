<?php

$loader = require 'vendor/autoload.php';
$loader->addClassMap(array(
	'Fixtures' => 'test/util/Fixtures.php',
	'Sample' => 'test/util/Sample.php',
	'SampleSqlite' => 'test/util/SampleSqlite.php',
	'SampleWithStringUid' => 'test/util/SampleWithStringUid.php',
));

date_default_timezone_set("Europe/Copenhagen");
error_reporting(E_ALL|E_STRICT);
openlog("libDb", LOG_PID | LOG_CONS, LOG_LOCAL0);

DiContainer::instance()->config = new Config2('test/config.ini');
DiContainer::instance()->log = Log::createFromConfig();

?>