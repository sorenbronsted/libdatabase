<?php

class Property {
  const INT = 0;
  const DECIMAL = 1;
  const STRING = 2;
  const DATE = 3;
  const CASE_NUMBER = 4;
  const CPR = 5;
  const BOOLEAN = 6;
  const PERCENT = 7;
  const TIMESTAMP = 8;

  /* Converts a string value to a value of $type.
   * If values is empty or null nothing is converted.
   * Some of the types are php primitives or objects like this:
   *   INT, DECIMAL, STRING, PERCENT => primitive
   *   DATO, CASE_NUMBER, CPR => object
   * Return a value converted acording to type.
   */
  public static function getValue($type, $value) {
    $result = $value;
    switch ($type) {
      case self::INT:
        if (is_string($value)) {
          $result = intval(str_replace('.','',$value));
        }
        break;
      case self::DECIMAL:
        // If value contain an comma ',' then this value i properly a danish decimal number
        // on the form 1.234,5678
        if (is_string($value) && strpos($value, ',')) {
          $result = str_replace(',', '.', str_replace('.','',$value));
        }
        break;
      case self::DATE:
      case self::TIMESTAMP:
        if (is_string($value) && strlen($value)) {
          $result = Date::parse($value);
        }
        break;
      case self::STRING:
        if (!is_string($value)) {
          $result = strval($value);
        }
        break;
      case self::CASE_NUMBER:
        if ((is_string($value)  && strlen($value)) || is_numeric($value)) {
          $result = CaseNumber::parse($value);
        }
        break;
      case self::CPR:
        if (is_string($value) && strlen($value)) {
          $result = Cpr::parse($value);
        }
        break;
      case self::BOOLEAN:
        if (is_string($value) && is_numeric($value)) {
          $result = ($value == 1 ? true : false);
        }
        break;
      case self::PERCENT:
        if (is_string($value) && strpos($value, ',')) {
          $result = str_replace(',', '.', str_replace('.','', str_replace('%', '', $value)));
        }
        else if (is_string($value) && strpos($value, '%')) {
          $result = str_replace('%', '', $value);
        }
        break;
      default:
        $result = $value;
    }
    return $result;
  }

  public static function isEmpty($type, $value) {
    if (is_null($value)) {
      return true;
    }
    $result = true;

    switch ($type) {
      case self::INT:
      case self::BOOLEAN:
      case self::DECIMAL:
      case self::PERCENT:
      case self::CPR:
      case self::TIMESTAMP:
      case self::DATE:
      case self::CASE_NUMBER:
        $result = false;
        break;
      case self::STRING:
        $result = strlen($value) == 0;
        break;
    }
    return $result;
  }
}