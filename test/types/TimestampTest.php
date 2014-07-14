<?php
require_once 'test/settings.php';

class TimestampTest extends PHPUnit_Framework_TestCase {

  public function testDefault() {
    $s = "2012-03-01 12:30:45";
    $t = Timestamp::parse($s);
    $this->assertNotNull($t);
    $this->assertEquals($s, $t->toString());
  }

  public function testAsDate() {
    $s = "2012-03-01";
    $t = Timestamp::parse($s);
    $this->assertNotNull($t);
    $this->assertEquals($s, $t->toString());
  }

	public function testdiff() {
		$ts1 = Timestamp::parse('01-01-2014 00:00:00');
		$ts2 = Timestamp::parse('01-01-2014 00:00:10');
		$this->assertEquals(10, $ts2->diff($ts1));
		$ts2 = Timestamp::parse('01-01-2014 00:10:00');
		$this->assertEquals(10 * 60, $ts2->diff($ts1));
		$ts2 = Timestamp::parse('01-01-2014 02:00:00');
		$this->assertEquals(2 * 60 * 60, $ts2->diff($ts1));
		$ts2 = Timestamp::parse('02-01-2014 00:00:00');
		$this->assertEquals(24 * 60 * 60, $ts2->diff($ts1));
	}
}

?>