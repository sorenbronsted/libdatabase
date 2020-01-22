<?php
namespace sbronsted;

use PHPUnit\Framework\TestCase;

require_once 'test/settings.php';

class DbObjectCrudMysqlTest extends TestCase {
  
	public function testCrud() {
		if (!extension_loaded('pdo_mysql')) {
			$this->markTestSkipped('extention pdo_mysql not loaded');
		}
    $sample = $this->create();
    $sample = $this->read($sample);
    $update = $this->update($sample);
    $this->delete($sample);
  }
  
  private function delete($sample) {
    SampleMysql::getByUid($sample->uid);
    $sample->destroy();
    try {
    	SampleMysql::getByUid($sample->uid);
      $this->fail("Expected exception");
    }
    catch (NotFoundException $e) {
      // Succes
    }
  }
  
  private function update($sample) {
		$sample->name = 'sletmig';
		$sample->save();
    $read = SampleMysql::getByUid($sample->uid);
    $this->compare($read, $sample);
    return $read;
  }
  
  private function read($sample) {
    $read = SampleMysql::getByUid($sample->uid);
    $this->compare($sample, $read);
    return $read;
  }
  
  private function compare($a, $b) {
    foreach($a->getProperties() as $name => $type) {
      $this->assertEquals($a->$name, $b->$name, $name);
    }
  }
  
  private function create() {
    $sample = Fixtures::newSampleMysql();
    $sample->save();
    return $sample;
  }
}
