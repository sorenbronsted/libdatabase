<?php
require_once 'test/settings.php';

class DbFactoryTest extends PHPUnit_Framework_TestCase {

  protected function tearDown() {
  }

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

}
