<?php

/*
 * This will map to a corresponding table named sample in lowercase.
 * The class must extend DbObject
 */
class SampleWithStringUid extends DbObject {
  
  // Each object must describe the properties of the table
  private static $properties = array(
    'uid' => Property::STRING,
  );
  
  // mandatory method used by DbObject
  public function getProperties() {
    return self::$properties;
  }
}
?>