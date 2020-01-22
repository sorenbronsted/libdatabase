<?php
namespace sbronsted;

require_once 'test/settings.php';

class Fixtures {

	public static function newSample() : Sample {
		$sample = new Sample();
		return self::populate($sample);
	}

	public static function newSampleSqlite() : Sample {
    $sample = new SampleSqlite();
		return self::populate($sample);
	}
  
	public static function newSampleMysql() : Sample {
		$sample = new SampleMysql();
		return self::populate($sample);
	}

	public static function newSampleMdb() : SampleMdb {
		$object = new SampleMdb();
		$object->name = 'test';
		return $object;
	}

	private static function populate(Sample $sample): Sample {
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
}
