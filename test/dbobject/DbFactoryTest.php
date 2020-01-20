<?php
namespace sbronsted;

use PHPUnit\Framework\TestCase;

require_once 'test/settings.php';

class DbFactoryTest extends TestCase {

  public function testMysql() {
		if (!extension_loaded('pdo_mysql')) {
			$this->markTestSkipped('extention pdo_odbc not loaded');
		}
		$cur = Db::query('mysql', "select 1 as value");
		$row = $cur->next();
		$this->assertEquals(1, $row['value']);
  }

  public function testSqlite() {
		if (!extension_loaded('pdo_sqlite')) {
			$this->markTestSkipped('extention pdo_pdo_sqlite not loaded');
		}
		$cur = Db::query('defaultDb', "select 1 as value");
		$row = $cur->next();
		$this->assertEquals(1, $row['value']);
  }

	public function testDb2() {
		if (!extension_loaded('pdo_ibm')) {
			$this->markTestSkipped('extention pdo_ibm not loaded');
		}
		$cur = Db::query('db2', "select 1 as value from sysibm.sysdummy1");
		//$cur = Db::query('db2', "select count(*) as value from skadelf");
		$row = $cur->next();
		$this->assertEquals(1, $row['VALUE']);
	}

	public function testUnknownDriver() {
		try {
			DbFactory::getConnection('unknown');
			$this->fail('Expected exception');
		}
		catch(ConnectionException $e) {
			$this->assertStringContainsString('dsn', $e->getMessage());
		}
	}
}
