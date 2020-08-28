<?php
namespace sbronsted;

/*
 * This will map to a corresponding table named sample in lowercase.
 * The class must extend DbObject
 */
class Sample extends DbObject {
  
  // Each object must describe the properties of the table
  private static $properties = array(
    'uid' => Property::INT,
    'case_number' => Property::CASE_NUMBER,
    'date_value' => Property::DATE,
    'datetime_value' => Property::TIMESTAMP,
    'cpr' => Property::CPR,
    'int_value' => Property::INT,
    'string_value' => Property::STRING,
    'decimal_value' => Property::DECIMAL,
    'boolean_value' => Property::BOOLEAN,
		'changed' => Property::STRING,
  );

  public $transient_value;

  // mandatory method used by DbObject
  public function getProperties() : array {
    return self::$properties;
  }
}
