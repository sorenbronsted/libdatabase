<?php
namespace sbronsted;

class SampleSqlite extends Sample {

	public static function createSchema() {
		$sql = "create table if not exists sample(
    	uid integer primary key autoincrement, 
    	case_number integer,
    	date_value date,
    	datetime_value datetime,
    	cpr integer,
    	int_value integer,
    	string_value varchar(16),
    	decimal_value decimal,
    	boolean_value integer,
    	changed varchar(10)
		)";
		self::$db = 'defaultDb';
		Db::exec(self::$db, $sql);
	}
}
