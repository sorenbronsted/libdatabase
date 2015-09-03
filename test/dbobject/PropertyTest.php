<?php
require_once 'test/settings.php';

class PropertyTest extends PHPUnit_Framework_TestCase {

	public function testGetValueInt() {
		$values = array('1' => 1, 2 => 2);
		foreach($values as $input => $result) {
			$val = Property::getValue(Property::INT, $input);
			$this->assertEquals($result, $val, $input);
			$this->assertEquals(true, is_int($val), $input);
			$this->assertEquals(false, is_string($val), $input);
			$this->assertEquals(false, is_float($val), $input);
		}
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
	}

	public function testGetValueDate() {
		$v = Property::getValue(Property::DATE, '31-12-2010');
		$d = Date::parse('31-12-2010');
		$this->assertEquals($d, $v);
	}

	public function testGetValueTimestamp() {
		$v = Property::getValue(Property::DATE, '31-12-2010 11:12:13');
		$d = Timestamp::parse('31-12-2010 11:12:13');
		$this->assertEquals($d, $v);
	}

	public function testGetValueString() {
		$v = Property::getValue(Property::STRING, 'abc');
		$this->assertEquals('abc', $v);
	}
}
