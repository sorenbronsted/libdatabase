<?php
namespace ufds;

use PHPUnit\Framework\TestCase;

require_once 'test/settings.php';

class PropertyTest extends TestCase {

	public function testGetNull() {
		$this->assertEquals(null, Property::getValue(Property::INT, null));
		$this->assertEquals(null, Property::getValue(Property::INT, 'null'));
	}

	public function testGetValueInt() {
		$values = array('1' => 1, 2 => 2);
		foreach($values as $input => $result) {
			$val = Property::getValue(Property::INT, $input);
			$this->assertEquals($result, $val, $input);
			$this->assertEquals(true, is_int($val), $input);
			$this->assertEquals(false, is_string($val), $input);
			$this->assertEquals(false, is_float($val), $input);
		}
		$v = Property::getValue(Property::INT, '');
		$this->assertEquals(0, $v);
	}

	public function testGetValueDecimal() {
		$values = array('1,1' => 1.1, '1.2' => 1.2);
		foreach($values as $input => $result) {
			$val = Property::getValue(Property::DECIMAL, $input);
			$this->assertEquals($result, $val, $input);
			$this->assertEquals(false, is_int($val), $input);
			$this->assertEquals(false, is_string($val), $input);
			$this->assertEquals(true, is_float($val), $input);
		}

		$this->assertEquals(1.0, Property::getValue(Property::DECIMAL, '1,0000'));
		$this->assertEquals(
			Property::getValue(Property::DECIMAL, '1,00'),
			Property::getValue(Property::DECIMAL, '1,0000')
		);
		$v = Property::getValue(Property::DECIMAL, '');
		$this->assertEquals(0, $v);

		$v = Property::getValue(Property::DECIMAL, 0.0);
		$this->assertEquals('0', "$v");
	}

	public function testGetValueDate() {
		$v = Property::getValue(Property::DATE, '31-12-2010');
		$d = Date::parse('31-12-2010');
		$this->assertEquals($d, $v);
		$v = Property::getValue(Property::DATE, Date::parse('31-12-2010'));
		$this->assertEquals($d, $v);
		$v = Property::getValue(Property::DATE, '');
		$this->assertEquals(null, $v);
	}

	public function testGetValueCpr() {
		$v = Property::getValue(Property::CPR, '0101010202');
		$d = Cpr::parse('0101010202');
		$this->assertEquals($d, $v);
		$v = Property::getValue(Property::CPR, Cpr::parse('0101010202'));
		$this->assertEquals($d, $v);
		$v = Property::getValue(Property::CPR, '');
		$this->assertEquals(null, $v);
	}

	public function testGetValueCaseNumber() {
		$v = Property::getValue(Property::CASE_NUMBER, '12/15');
		$d = CaseNumber::parse('12/15');
		$this->assertEquals($d, $v);
		$v = Property::getValue(Property::CASE_NUMBER, CaseNumber::parse('12/15'));
		$this->assertEquals($d, $v);
		$v = Property::getValue(Property::CASE_NUMBER, '');
		$this->assertEquals(null, $v);
	}

	public function testGetValueTimestamp() {
		$v = Property::getValue(Property::TIMESTAMP, '31-12-2010 11:12:13');
		$d = Timestamp::parse('31-12-2010 11:12:13');
		$this->assertEquals($d, $v);
		$v = Property::getValue(Property::TIMESTAMP, Timestamp::parse('31-12-2010 11:12:13'));
		$this->assertEquals($d, $v);
		$v = Property::getValue(Property::TIMESTAMP, '');
		$this->assertEquals(null, $v);
	}

	public function testGetValueString() {
		$v = Property::getValue(Property::STRING, 'abc');
		$this->assertEquals('abc', $v);
		$v = Property::getValue(Property::STRING, '');
		$this->assertEquals('', $v);
		$this->assertNotNull($v);
	}

	public function testGetValueBoolean() {
		$v = Property::getValue(Property::BOOLEAN, '1');
		$this->assertEquals(true, $v);
		$v = Property::getValue(Property::BOOLEAN, '0');
		$this->assertEquals(false, $v);
		$v = Property::getValue(Property::BOOLEAN, 1);
		$this->assertEquals(true, $v);
		$v = Property::getValue(Property::BOOLEAN, 0);
		$this->assertEquals(false, $v);
		$v = Property::getValue(Property::BOOLEAN, '');
		$this->assertEquals(false, $v);
		$this->assertNotNull($v);
		$v = Property::getValue(Property::BOOLEAN, null);
		$this->assertFalse(false, $v);
		$this->assertNotNull($v);
	}

	public function testIsEqual() {
		$v1 = Property::getValue(Property::INT, '1');
		$v2 = Property::getValue(Property::INT, '1');
		$this->assertTrue(Property::isEqual(Property::INT, $v1, $v2));

		$v1 = Property::getValue(Property::INT, 0);
		$v2 = Property::getValue(Property::INT, null);
		$this->assertFalse(Property::isEqual(Property::INT, $v1, $v2));

		$v1 = Property::getValue(Property::DATE, '28-09-2015');
		$v2 = Property::getValue(Property::DATE, '28-09-2015');
		$this->assertTrue(Property::isEqual(Property::DATE, $v1, $v2));

		$v1 = Property::getValue(Property::DATE, '');
		$v2 = Property::getValue(Property::DATE, '');
		$this->assertFalse(Property::isEqual(Property::DATE, $v1, $v2));

		$v1 = Property::getValue(Property::DATE, '28-09-2015');
		$v2 = Property::getValue(Property::DATE, '');
		$this->assertFalse(Property::isEqual(Property::DATE, $v1, $v2));	}
}
