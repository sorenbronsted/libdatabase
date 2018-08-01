<?php
namespace ufds;

use PHPUnit\Framework\TestCase;

require_once 'test/settings.php';

/*
 * This test a sqlite memory database, where the SampleSqlite has a overridden db property
 * which tell which driver is used and therefore the database.
 */
class DbObjectCrudSqliteTest extends TestCase {
  
	public static function setUpBeforeClass() {
		SampleSqlite::createSchema();
	}

	public function testCrud() {
    $sample = $this->create();
    $sample = $this->read($sample);
    $update = $this->update($sample);
    $this->delete($sample);
  }
  
  private function delete($sample) {
    $read = SampleSqlite::getByUid($sample->uid);
    $sample->destroy();
    try {
    $read = SampleSqlite::getByUid($sample->uid);
      $this->fail("Expected exception");
    }
    catch (NotFoundException $e) {
      // Succes
    }
  }
  
  private function update($sample) {
		$sample->name = 'sletmig';
		$sample->save();
    $read = SampleSqlite::getByUid($sample->uid);
    $this->compare($read, $sample);
    return $read;
  }
  
  private function read($sample) {
    $read = SampleSqlite::getByUid($sample->uid);
    $this->compare($sample, $read);
    return $read;
  }
  
  private function compare($a, $b) {
    foreach($a->getProperties() as $name => $type) {
      $this->assertEquals($a->$name, $b->$name, $name);
    }
  }
  
  private function create() {
    $sample = Fixtures::newSampleSqlite();
    $sample->save();
    return $sample;
  }
}
