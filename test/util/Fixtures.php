<?php
namespace sbronsted;

require_once 'test/settings.php';

class Fixtures {

  public static function newSample() {
    $sample = new Sample();
    $sample->case_number = '10/01';
    $sample->date_value = '21-10-2012';
    $sample->datetime_value = '21-10-2012 11:11:11';
    $sample->cpr = '0102031234';
    $sample->int_value = "117";
    $sample->string_value = "test's";
    $sample->decimal_value = 1234567.89;
    $sample->boolean_value = 1;
    return $sample;
  }
  
	public static function newSampleMysql() {
		$object = new SampleSqlite();
		$object->name = 'test';
		return $object;
	}

	public static function newSampleMdb() {
		$object = new SampleMdb();
		$object->name = 'test';
		return $object;
	}
}
