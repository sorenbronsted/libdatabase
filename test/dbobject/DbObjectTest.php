<?php
namespace sbronsted;

use PHPUnit\Framework\TestCase;
use stdClass;

require_once 'test/settings.php';

class DbObjectTest extends TestCase {

	public static function setUpBeforeClass(): void {
		if (extension_loaded('pdo_sqlite')) {
			SampleSqlite::createSchema();
		}
	}

	protected function setUp() : void {
		if (extension_loaded('pdo_sqlite')) {
			Db::exec('defaultDb', "delete from sample");
		}
	}

  public function testHasChanged() {
    $sample = Fixtures::newSample();
    $sample->save();
    
    $sample->case_number = "19990001";
    $this->assertTrue($sample->hasFieldChanged('case_number'));
    
    $sample = Sample::getByUid($sample->uid);
    $sample->case_number = "10/01";
    $this->assertFalse($sample->hasFieldChanged('case_number'));
    
    $sample->destroy();
  }

  public function testEmpty() {
  	$sample = Fixtures::newSample();
  	$this->assertFalse(empty($sample->cpr));
  	$sample->cpr = '';
  	$this->assertTrue(empty($sample->cpr));
  	$this->assertTrue(empty($sample->unknown));
  }

  public function testMultipleUpdates() {
    $sample = Fixtures::newSample();
    $sample->save();
    $sample->int_value = 1;
    $sample->int_value = 2;
    $changedFields = $sample->getChanged();
    $this->assertNotNull($changedFields);
    $this->assertEquals(1, count($changedFields));
    $sample->destroy();
  }
	
	public function testDestroyBy() {
    $sample = Fixtures::newSample();
    $sample->save();
		$list = Sample::getBy(array('uid' => $sample->uid));
		$this->assertEquals(1, count($list));
		Sample::destroyBy(array('uid' => $sample->uid));
		$list = Sample::getBy(array('uid' => $sample->uid));
		$this->assertEquals(0, count($list));
	}
	
	public function testSetDataStringUid() {
		$data = array('uid' => 'key1');
		$object = new SampleWithStringUid();
		$this->assertEquals(0, $object->uid);
		$object->setData($data);
		$this->assertEquals($data['uid'], $object->uid);
		$data = array('uid' => '1');
		$object->setData($data);
		$this->assertEquals($data['uid'], $object->uid);
		$data = array('uid' => '0');
		$object->setData($data);
		$this->assertEquals($data['uid'], $object->uid);
		$data = array('uid' => null);
		$object->setData($data);
		$this->assertEquals($data['uid'], $object->uid);
	}
	
	public function testChanged() {
    $sample = Fixtures::newSample();
		$sample->save();

		// Should not change when assigned same value
    $sample->case_number = '10/01';
    $sample->date_value = '21-10-2012';
    $sample->datetime_value = '21-10-2012 11:11:11';
    $sample->cpr = '0102031234';
    $sample->int_value = 117;
    $sample->string_value = "test's";
    $sample->decimal_value = 1234567.89;
    $sample->boolean_value = "1";
		$this->assertEquals(0, count($sample->getChanged()));

    $sample->case_number = '10/02';
    $sample->date_value = '21-10-2013';
    $sample->datetime_value = '21-10-2012 11:11:12';
    $sample->cpr = '0102031235';
    $sample->int_value = "118";
    $sample->string_value = "tests";
    $sample->decimal_value = 1234567.99;
    $sample->boolean_value = "2";
		$this->assertEquals(8, count($sample->getChanged()));
	}

	public function testSelect() {
		$sample = Fixtures::newSample();
		$sample->save();
		$qbe = array('uid' => $sample->uid);
		$orderby = array('uid');

		$cursor = Db::select('sample', $qbe,  DbObject::$db, $orderby);
		$this->assertTrue($cursor->hasNext());
	}

	public function testGetAll() {
		$sample = Fixtures::newSample();
		$sample->save();
		$objects = Sample::getAll(array('uid'));
		$this->assertEquals(1, count($objects));
	}

	public function testGetWhere() {
		$sample = Fixtures::newSample();
		$sample->save();
		$objects = Sample::getWhere("uid = ".$sample->uid);
		$this->assertEquals(1, count($objects));
		$objects = Sample::getWhere("uid = :p1", array('p1' => $sample->uid));
		$this->assertEquals(1, count($objects));
	}

	public function testGetQbe() {
		$sample = Fixtures::newSample();
		$sample->date_value = null;
		$sample->save();
		$objects = Sample::getBy(array('int_value' => 117));
		$this->assertEquals(1, count($objects));
		$objects = Sample::getBy(array('string_value' => 'test%'));
		$this->assertEquals(1, count($objects));
		$objects = Sample::getBy(array('date_value' => null));
		$this->assertEquals(1, count($objects));
	}

	public function testSetNullValues() {
		$sample = Fixtures::newSample();
		$sample->save();

		$object = Sample::getByUid($sample->uid);
		$this->assertNotNull($object->date_value);
		$object->date_value = null;
		$object->save();

		$object = Sample::getByUid($sample->uid);
		$this->assertNull($object->date_value);
	}

	public function testGetProperties() {
		$sample = Fixtures::newSample();
		$this->assertEquals(10, count($sample->getProperties()));
	}

	public function testContructor() {
		$sample = new Sample(['int_value' => 5]);
		$this->assertEquals(5, $sample->int_value);

  	$values = new stdClass();
  	$values->int_value = 10;
  	$sample = new Sample($values);
  	$this->assertEquals($values->int_value, $sample->int_value);

		$sample = new Sample();
		$this->assertEquals(0, $sample->uid);
	}

	public function testSetData() {
		$sample = Fixtures::newSample();
		$sample->setData(['changed' => 'some value']);
		$this->assertTrue(is_array($sample->getChanged()));
		$this->assertEquals(9, count($sample->getChanged()));
	}
}
