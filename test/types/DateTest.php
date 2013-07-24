<?php
require_once 'PHPUnit/Autoload.php';
require_once 'test/settings.php';

class DateTest extends PHPUnit_Framework_TestCase {

  public function testValid() {
    $date = Date::parse("01-01-2001");
    $this->assertEquals("2001-01-01", $date->toString());
    $this->assertEquals("01-01-2001", $date->format(Date::FMT_DA));
  }

  public function testNotValid() {
    try {
      $date = Date::parse("01012001");
      $this->fail("IllegalArgumentException expected");
    }
    catch (IllegalArgumentException $e) {
      $this->assertTrue(true);
    }
  }

  public function testYear() {
    $date = Date::parse("01-01-2001");
    $this->assertEquals(2001, $date->year);
  }
  
  public function testDay() {
    $date = Date::parse("01-01-2001");
    $this->assertEquals(1, $date->day);
  }
  
  public function testDate() {
    $date = Date::parse("01-01-2001");
    $this->assertEquals(20010101, $date->date);
    $this->assertEquals("20010101", $date->date);
  }
  
  public function testEquals() {
    $date1 = Date::parse("01-01-2001");
    $date2 = Date::parse("2001-01-01");
    $date3 = Date::parse("2001-02-01");
    $this->assertEquals($date1, $date2);
    $this->assertnotEquals($date1, $date3);
  }
  
  public function testEmpty() {
    $d = Date::parse("0000-00-00");
    $this->assertequals(true, is_null($d));
    
    try {
      $d = Date::parse(10203040506);
      $this->fail("Exception exptected");
    }
    catch(IllegalArgumentException $e) {
      // Success
    }
  }
  
  public function testIsAfter() {
    $date1 = Date::parse("01-01-2001");
    $date2 = Date::parse("01-02-2001");
    $this->assertTrue($date2->isAfter($date1));
    $this->assertFalse($date1->isAfter($date2));
  }
  
  public function testIsBefore() {
    $date1 = Date::parse("01-02-2001");
    $date2 = Date::parse("01-01-2001");
    $this->assertTrue($date2->isBefore($date1));
    $this->assertFalse($date1->isBefore($date2));
  }
  
  public function testBigDate() {
    $date1 = Date::parse("01-01-2100");
    $this->assertEquals(21000101, $date1->date);
  }
  
  public function testDiff() {
    $date1 = Date::parse("01-01-2100");
    $date2 = Date::parse("10-01-2100");
    $this->assertEquals($date2->diff($date1), 9);
    $this->assertEquals($date1->diff($date2), -9);
  }
  
  public function testSetDay() {
    $date = Date::parse("10-01-2100");
    $date->day = 2;
    $this->assertEquals(2, $date->day);
  }

  public function testSetMonth() {
    $date = Date::parse("01-01-2001");
    $date->month += 2;
    $this->assertEquals(3, $date->month);
  }
  
  public function testSetYear() {
    $date = Date::parse("01-01-2001");
    $date->year += 9;
    $this->assertEquals(2010, $date->year);
  }

  public function testConstructDateTime() {
    $dt = new DateTime("now");
    $date = new Date($dt);
    $this->assertEquals($date->date, $dt->format("Ymd"));
  }

  public function testCopyConstruct() {
    $date1 = Date::parse("01-01-2010");
    $date2 = new Date($date1);
    $this->assertEquals($date1, $date2);
  }
  
  public function testConstructWithFormat() {
    $s = "20091229";
    $date = Date::parse($s, Date::FMT_YMD);
    $this->assertEquals($s, $date->format("Ymd"));
  }
  
  public function testWithSimpleXml() {
    $xml = "<?xml version='1.0' standalone='yes'?><document><date>20091229</date></document>";
    
    $doc = new SimpleXMLElement($xml);
    //var_dump($doc->date);
    //var_dump((string)$doc->date);
    $date = Date::parse((string)$doc->date, Date::FMT_YMD);
    $this->assertEquals("20091229", $date->format("Ymd"));
  }
  
  public function testWithTime() {
    $dt = new DateTime("now");
    $d = new Date($dt);
    $this->assertEquals($dt->format("Y-m-d H:i:s"), $d->format(Date::FMT_MYSQL_LONG));
    $this->assertEquals($dt->format("His"), $d->time);
    $this->assertEquals($dt->format("YmdHis"), $d->datetime);
    $this->assertTrue($d->equals(new Date($dt)));
  }
  
  public function testDefault() {
    $d = new Date();
    $this->assertTrue(strlen($d->toString()) > 0);
  }
  
  public function testIllegalArgument() {
    try {
      new Date("01-01-01");
      $this->fail("Exception expected");
    }
    catch (IllegalArgumentException $e) {
      // success
    }
  }
  
  public function testRoll() {
    $date = Date::parse("02-01-1950");
    $interval = new DateInterval("P3M");
    $date->rollForward($interval);
    $this->assertEquals(Date::parse("02-04-1950"), $date);
    $date->rollBackward($interval);
    $this->assertEquals(Date::parse("02-01-1950"), $date);
  }
  
  public function testIsBetween() {
    $date = Date::parse('01-01-1910');
    $from = Date::parse('01-01-1900');
    $to = Date::parse('01-01-1930');
    $this->assertTrue($date->isBetween($from, $to));
    
    // test boundaries
    $this->assertTrue($date->isBetween($date, $to));
    $this->assertTrue($date->isBetween($from, $date));
    $this->assertTrue($date->isBetween($date, $date));
    
    // false cases
    $date = Date::parse('01-01-1980');
    $this->assertFalse($date->isBetween($from, $to));
  }
}
