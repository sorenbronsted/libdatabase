<?php
require_once 'PHPUnit/Autoload.php';
require_once 'test/settings.php';

class CaseNumberTest extends PHPUnit_Framework_TestCase {

  protected function setUp() {
    $this->values = array(
      20110010 => 20110010,
      "20110010" => 20110010,
      "10/2011" => 20110010,
      "10/11" => 20110010,
      "1/1" => 20010001,
      "1/2001" => 20010001
    );
  }
  
  public function testToNumber() {
    foreach($this->values as $src => $tst) {    
      $cn = new CaseNumber($src);
      $this->assertEquals($tst, $cn->toNumber());
    }
  }
  
  public function testIsEquals() {
    foreach($this->values as $src => $tst) {    
      $srcCn = new CaseNumber($src);
      $tstCn = new CaseNumber($tst);
      $this->assertTrue($tstCn->isEqual($srcCn));
    }
    
    $srcCn = new CaseNumber("10/10");
    $tstCn = new CaseNumber("11/11");
    $this->assertFalse($tstCn->isEqual($srcCn));
  }
  
  public function testToString() {
    $cn = new CaseNumber(20110001);
    $this->assertTrue(is_string($cn->__toString()));
    $this->assertEquals("20110001", $cn);
  }
  
  public function testIsNull() {
    $cn = new CaseNumber(20110001);
    $this->assertTrue(!$cn->isNull());
    $cn = new CaseNumber(null);
    $this->assertTrue($cn->isNull());
  }
}
