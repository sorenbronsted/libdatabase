<?php
namespace ufds;

class SampleSqlite extends DbObject {
	// This the overridden database name
  public static $db = 'sqlite';
	
  private static $properties = array(
    'uid' => Property::INT,
    'name' => Property::STRING,
  );

	public static function createSchema() {
		$sql = "create table samplesqlite(uid integer primary key autoincrement, name varchar(20))";
		Db::exec(static::$db, $sql);
	}
	
  public function getProperties() {
    return self::$properties;
  }
}
