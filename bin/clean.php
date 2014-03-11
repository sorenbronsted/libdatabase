<?php
require_once 'test/settings.php';

$content = file_get_contents('database/sql/clean.sql');
$lines = explode('\n', $content);
foreach($lines as $line) {
	Db::exec('defaultDb', $line);
}

?>
