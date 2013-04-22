<?php

require_once 'PHPUnit/Autoload.php';
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

}

?>