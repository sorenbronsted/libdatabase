<?php
require_once 'test/settings.php';

class PropertyIsEmptyTest extends PHPUnit_Framework_TestCase {
  protected $backupGlobals = FALSE;
  
  public function testInt() {
    $this->assertEquals(false, Property::isEmpty(Property::INT, 0));
    $this->assertEquals(false, Property::isEmpty(Property::INT, 1));
    $this->assertEquals(true, Property::isEmpty(Property::INT, null));
  }
  
  public function testDecimal() {
    $this->assertEquals(false, Property::isEmpty(Property::DECIMAL, 0));
    $this->assertEquals(false, Property::isEmpty(Property::DECIMAL, 1.1));
    $this->assertEquals(true, Property::isEmpty(Property::DECIMAL, null));
  }
  
  public function testBoolean() {
    $this->assertEquals(false, Property::isEmpty(Property::BOOLEAN, true));
    $this->assertEquals(false, Property::isEmpty(Property::BOOLEAN, false));
    $this->assertEquals(true, Property::isEmpty(Property::BOOLEAN, null));
    $this->assertEquals(true, Property::getValue(Property::BOOLEAN, 1));
    $this->assertEquals(true, Property::getValue(Property::BOOLEAN, "1"));
    $this->assertEquals(true, Property::getValue(Property::BOOLEAN, true));
    $this->assertEquals(false, Property::getValue(Property::BOOLEAN, 0));
    $this->assertEquals(false, Property::getValue(Property::BOOLEAN, "0"));
    $this->assertEquals(false, Property::getValue(Property::BOOLEAN, false));
  }
  
  public function testString() {
    $this->assertEquals(false, Property::isEmpty(Property::STRING, "abc"));
    $this->assertEquals(true, Property::isEmpty(Property::STRING, ""));
    $this->assertEquals(true, Property::isEmpty(Property::STRING, null));
  }
  
  public function testCpr() {
    $this->assertEquals(false, Property::isEmpty(Property::CPR, new Cpr("0101010101")));
    $this->assertEquals(true, Property::isEmpty(Property::CPR, null));
  }
  
  public function testCaseNumber() {
    $this->assertEquals(false, Property::isEmpty(Property::CASE_NUMBER, new CaseNumber(20100001)));
    $this->assertEquals(true, Property::isEmpty(Property::CASE_NUMBER, null));
  }
  
  public function testDate() {
    $this->assertEquals(false, Property::isEmpty(Property::DATE, Date::parse("01-01-2011")));
    $this->assertEquals(true, Property::isEmpty(Property::DATE, Date::parse("0000-00-00")));
    $this->assertEquals(true, Property::isEmpty(Property::DATE, null));
  }

  public function testPercent() {
    $this->assertEquals(false, Property::isEmpty(Property::PERCENT, 0));
    $this->assertEquals(false, Property::isEmpty(Property::PERCENT, 1.1));
    $this->assertEquals(true, Property::isEmpty(Property::PERCENT, null));
  }
}

?>
