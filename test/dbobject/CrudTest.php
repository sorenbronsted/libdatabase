<?php
require_once 'PHPUnit/Autoload.php';
require_once 'test/settings.php';

class CrudTest extends PHPUnit_Framework_TestCase {
  
  protected function tearDown() {
    Db::exec("delete from sample");
  }
  
  public function testCrud() {
    $sample = $this->create();
    $sample = $this->read($sample);
    $update = $this->update($sample);
    $this->delete($sample->uid);
  }
  
  private function delete($uid) {
    $sample = Sample::getByUid($uid);
    $sample->destroy();
    try {
      $sample = Sample::getByUid($uid);
      $this->fail("Expected exception");
    }
    catch (NotFoundException $e) {
      // Succes
    }
  }
  
  private function update($sample) {
    $updated = new Sample($sample->getData());
    $updated->case_number = "19990011";
    $updated->date_value = '01-01-2012';
    $updated->cpr = '0302014321';
    $updated->int_value = "711";
    $updated->decimal_value = "9.876.543,21";
    $updated->boolean_value = "0";
    $updated->save();
    $read = Sample::getOneBy(array('string_value' => 'test'));
    $this->compare($updated, $read);
    return $updated;
  }
  
  private function read($sample) {
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
    $sample = new Sample();
    $sample->case_number = '10/01';
    $sample->date_value = '21-10-2012';
    $sample->cpr = '0102031234';
    $sample->int_value = "117";
    $sample->string_value = 'test';
    $sample->decimal_value = 1234567.89;
    $sample->boolean_value = "1";
    
    $sample->save();
    $this->assertTrue($sample->uid > 0);
    return $sample;
  }
}
?>