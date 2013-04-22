<?php
require_once 'PHPUnit/Autoload.php';
require_once 'test/settings.php';

class CaseNumberTest extends PHPUnit_Framework_TestCase {
  private $values;
  
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
  
  public function testParse() {
    foreach($this->values as $src => $tst) {    
      $cn = CaseNumber::parse($src);
      $this->assertNotNull($cn, $src);
      $this->assertEquals($tst, $cn->toNumber());
    }
  }
  
  public function testIsEquals() {
    foreach($this->values as $src => $tst) {    
      $srcCn = CaseNumber::parse($src);
      $this->assertNotNull($srcCn);
      $tstCn = CaseNumber::parse($tst);
      $this->assertNotNull($tstCn);
      $this->assertTrue($tstCn->isEqual($srcCn));
    }
    
    $srcCn = CaseNumber::parse("10/10");
    $this->assertNotNull($srcCn);
    $tstCn = CaseNumber::parse("11/11");
    $this->assertNotNull($tstCn);
    $this->assertFalse($tstCn->isEqual($srcCn));
  }
  
  public function testToString() {
    $cn = new CaseNumber(20110001);
    $this->assertTrue(is_string($cn->__toString()));
    $this->assertEquals("20110001", $cn);
  }
  
  public function testIllegalArgument() {
    $values = array(null, "abc", 10);
    foreach($values as $value) {
      try {
        new CaseNumber($value);
        $this->fail("Exception expected");
      }
      catch (IllegalArgumentException $e) {
        // success
      }
    }
  }
}
