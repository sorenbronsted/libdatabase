<?php
require_once 'PHPUnit/Autoload.php';
require_once 'test/settings.php';

class PropertyTest extends PHPUnit_Framework_TestCase {

  public function testGetValueInt() {
    $value = "1.234,5678";
    $this->assertEquals(1234, Property::getValue(Property::INT, $value));
    $value = "1234";
    $this->assertEquals(1234, Property::getValue(Property::INT, $value));
  }

  public function testGetValueDecimal() {
    $value = "1.234,5678";
    $this->assertEquals(1234.5678, Property::getValue(Property::DECIMAL, $value));
    $value = "1.234.567,89";
    $this->assertEquals(1234567.89, Property::getValue(Property::DECIMAL, $value));
    $value = "1234,56";
    $this->assertEquals(1234.56, Property::getValue(Property::DECIMAL, $value));
    $value = "1234.56";
    $this->assertEquals(1234.56, Property::getValue(Property::DECIMAL, $value));
  }
  
  public function testGetValueDate() {
    $value = "22-05-2011";
    $this->assertEquals("2011-05-22", Property::getValue(Property::DATE, $value));
    $value = "22-05-2011";
    $this->assertEquals(new Date("2011-05-22"), Property::getValue(Property::DATE, $value));
    $value = "";
    $this->assertEquals("", Property::getValue(Property::DATE, $value));
    $value = null;
    $this->assertEquals(null, Property::getValue(Property::DATE, $value));
  }
  
  public function testGetValueBoolean() {
    $value = "1";
    $this->assertEquals(true, Property::getValue(Property::BOOLEAN, $value));
    $value = "0";
    $this->assertEquals(false, Property::getValue(Property::BOOLEAN, $value));
  }
  
  public function testGetValueCaseNumber() {
    $value = "19980011";
    $this->assertEquals(new CaseNumber(19980011), Property::getValue(Property::CASE_NUMBER, $value));
    $value = null;
    $this->assertEquals(null, Property::getValue(Property::CASE_NUMBER, $value));
    $value = "";
    $this->assertEquals("", Property::getValue(Property::CASE_NUMBER, $value));
  }
  
  public function testGetValueCpr() {
    $value = "0101010001";
    $this->assertEquals(new Cpr($value), Property::getValue(Property::CPR, $value));
    $value = null;
    $this->assertEquals(null, Property::getValue(Property::CPR, $value));
    $value = "";
    $this->assertEquals("", Property::getValue(Property::CPR, $value));
  }
  
  public function testIsNull() {
    $cn = new CaseNumber(19980011);
    $this->assertTrue(!Property::isEmpty(Property::CASE_NUMBER, $cn));
    $cn = new CaseNumber(null);
    $this->assertTrue(Property::isEmpty(Property::CASE_NUMBER, $cn));
  }
}
