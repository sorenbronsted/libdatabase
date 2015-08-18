<?php
require_once 'test/settings.php';

class DbObjectTest extends PHPUnit_Framework_TestCase {

  protected function tearDown() {
    Db::exec('defaultDb', "delete from sample");
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

  public function testUnknownProperty() {
    $sample = Fixtures::newSample();
    $sample->unknown = "test";
    $this->assertEquals(null, $sample->unknown);
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

		$cursor = Db::select('sample', $qbe, $orderby, DbObject::$db);
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
	}

	public function testGetProperties() {
		$sample = Fixtures::newSample();
		$this->assertEquals(9, count($sample->getProperties()));
	}
}

?>