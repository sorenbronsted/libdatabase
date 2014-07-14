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
    $this->assertTrue($sample->hasFieldChanged('case_number'));
    
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
}

?>