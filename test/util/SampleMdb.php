<?php
namespace sbronsted;

class SampleMdb extends DbObject {
	// This the overridden database name
  public static $db = 'access';
	
  private static $properties = array(
    'uid' => Property::INT,
    'name' => Property::STRING,
  );

  public function getProperties() : array {
    return self::$properties;
  }
}
