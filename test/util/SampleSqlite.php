<?php
namespace sbronsted;

class SampleSqlite extends DbObject {
	// This the overridden database name
  public static $db = 'defaultDb';
	
  private static $properties = array(
    'uid' => Property::INT,
    'name' => Property::STRING,
  );

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
    	boolean_value integer
		)";
		Db::exec(static::$db, $sql);
	}
	
  public function getProperties() {
    return self::$properties;
  }
}
