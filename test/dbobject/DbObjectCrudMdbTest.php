<?php
namespace sbronsted;

use PHPUnit\Framework\TestCase;

require_once 'test/settings.php';

/*
 * This is a basic testcase for reading an mdb file (MS Access database)
 * To make this work one must install the following packages:
 * libmdbodbc1, php-odbc, odbc-mdbtools
 *
 * If odbc can not find libodbccr.so, then you need to make a missing symlink in /usr/lib/x86_64-linux-gnu
 * ln -s libodbccr.so.2 libodbccr.so
 *
 */
class DbObjectCrudMdbTest extends TestCase {
  
	public function testBasic() {
		if (!extension_loaded('pdo_odbc')) {
			$this->markTestSkipped('extention pdo_odbc not loaded');
		}
		$objects = SampleMdb::getAll();
		$this->assertEquals(2, count($objects));
		$this->assertEquals(1, $objects[0]->uid);
		$this->assertEquals('Person1', $objects[0]->name);
	}
}
