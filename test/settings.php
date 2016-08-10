<?php
namespace ufds;

$loader = require 'vendor/autoload.php';
$loader->addPsr4('ufds\\', __DIR__.'/util');

date_default_timezone_set("Europe/Copenhagen");
error_reporting(E_ALL|E_STRICT);
openlog("libDb", LOG_PID | LOG_CONS, LOG_LOCAL0);

$dic = DiContainer::instance();
$dic->config = new Config2('test/libdatabase.ini');
$dic->log = Log::createFromConfig();

