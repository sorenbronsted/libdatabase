<?php
require_once 'PHPUnit/Autoload.php';
require_once 'test/settings.php';

class DbObjectTest extends PHPUnit_Framework_TestCase {

  protected function tearDown() {
    Db::exec("delete from sample");
  }

  public function testHasChanged() {
    $sample = Fixtures::newSample();
    $sample->save();
    
    $sample->case_number = "19990001";
    $this->assertTrue($sample->hasFieldChanged('case_number'));
    
    $sample = Sample::getByUid($sample->uid);
    $sample->case_number = "10/01";
    $this->assertTrue(!$sample->hasFieldChanged('case_number'));
    
    $sample->destroy();
  }

  public function testUnknownProperty() {
    $sample = Fixtures::newSample();
    $sample->unknown = "test";
    $this->assertEquals(null, $sample->unknown);
  }
  
}

?>