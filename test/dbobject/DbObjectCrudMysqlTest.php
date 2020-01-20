<?php
namespace sbronsted;

use PHPUnit\Framework\TestCase;

require_once 'test/settings.php';

/*
 * This test a sqlite memory database, where the SampleSqlite has a overridden db property
 * which tell which driver is used and therefore the database.
 */
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
		Sample::$db = 'mysql';
    Sample::getByUid($sample->uid);
    $sample->destroy();
    try {
    	Sample::getByUid($sample->uid);
      $this->fail("Expected exception");
    }
    catch (NotFoundException $e) {
      // Succes
    }
  }
  
  private function update($sample) {
		Sample::$db = 'mysql';
		$sample->name = 'sletmig';
		$sample->save();
    $read = Sample::getByUid($sample->uid);
    $this->compare($read, $sample);
    return $read;
  }
  
  private function read($sample) {
		Sample::$db = 'mysql';
    $read = Sample::getByUid($sample->uid);
    $this->compare($sample, $read);
    return $read;
  }
  
  private function compare($a, $b) {
    foreach($a->getProperties() as $name => $type) {
      $this->assertEquals($a->$name, $b->$name, $name);
    }
  }
  
  private function create() {
    $sample = Fixtures::newSample();
		$sample::$db = 'mysql';
    $sample->save();
    return $sample;
  }
}
