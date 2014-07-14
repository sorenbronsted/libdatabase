<?php
require_once 'test/settings.php';

class CprTest extends PHPUnit_Framework_TestCase {

  public function testValid() {
    $cprs = array(
      "19" => "0103251234",
      "19" => "0103502234",
      "19" => "0103753234",
      "19" => "0103804234",
      "19" => "0103909234",
      "19" => "010390AAA1",
      "20" => "0103094234",
      "20" => "0103258234",
      "18" => "0103605234",
      "18" => "0103706234",
      "18" => "0103807234",
      "18" => "0103908234",
    );
    foreach ($cprs as $key => $cpr_number) {
      try {
        $cpr = new Cpr($cpr_number);
        $century = $cpr->getCentury();
        $this->assertEquals($key, $century, $cpr_number);
      }
      catch (CprException $e) {
        $this->fail("Unexpected exception");
      }
    }
  }

  public function testNotValid() {
    $cprs = array("0103405234", "0103406234", "0103407234", "0103408234");
    foreach ($cprs as $cpr_number) {
      try {
        $cpr = new Cpr($cpr_number);
        $century = $cpr->getCentury();
        $this->fail("Expected exception for $cpr_number");
      }
      catch (CprException $e) {
        $this->assertTrue(true);
      }
    }
  }
  
  public function testSex() {
    $female = new Cpr("0103251224");
    $this->assertEquals(false, $female->isMale());
    $male = new Cpr("0103251235");
    $this->assertEquals(true, $male->isMale());
  }
  
  public function testEmpty() {
    foreach(array("", null) as $value) {
      $value = Cpr::parse($value);
      $this->assertEquals(null, $value);
    }
  }
  
  public function testGetDate() {
    $male = new Cpr("0103251235");
    $date = $male->getDate();
    $this->assertInstanceOf("Date", $date);
    $this->assertEquals(Date::parse("1925-03-01"), $date);
  }
  
  public function testGetAgeAt() {
    $male = new Cpr("0103251235");
    $age = $male->getAgeAt("20-01-1952");
    $this->assertEquals(1952-1925, $age);
    $age = $male->getAgeAt("1952-01-20");
    $this->assertEquals(1952-1925, $age);
  }
}
