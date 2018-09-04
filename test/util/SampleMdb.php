<?php
namespace ufds;

class SampleMdb extends DbObject {
	// This the overridden database name
  public static $db = 'access';
	
  private static $properties = array(
    'uid' => Property::INT,
    'name' => Property::STRING,
  );

  public function getProperties() {
    return self::$properties;
  }
}
