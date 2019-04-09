<?php
namespace ufds;

use PHPUnit\Framework\TestCase;

require_once 'test/settings.php';

class DbFactoryTest extends TestCase {

  public function testMysql() {
		$cur = Db::query('defaultDb', "select 1 as value");
		$row = $cur->next();
		$this->assertEquals(1, $row['value']);
  }

  public function testSqlite() {
		$cur = Db::query('sqlite', "select 1 as value");
		$row = $cur->next();
		$this->assertEquals(1, $row['value']);
  }

	public function testDb2() {
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
