<?php

class Cpr {
  private $number;
  
  public function __construct($number) {
    if (strlen($number) != 10) {
      throw new IllegalArgumentException($number, __FILE__, __LINE__);
    }
    $this->number = $number;
  }
  
  public function getDate() {
    $century = $this->getCentury();
    return Date::parse(substr($this->number,0,4).$century.substr($this->number,4,2), "dmY");
  }
  
  public function getAgeAt($date) {
    if (is_string($date)) {
      $date = Date::parse($date);
    }
    $birthDate = $this->getDate();
    return $date->year - $birthDate->year;
  }
  
  public function isMale() {
    return intval(substr($this->number,9,1)) % 2 == 1;
  }
  
  public function getCentury() {
    $result = "";
    $sCentury = substr($this->number, 6, 1);
    if ($sCentury == "A") { //Ufds foreigner
      $result = "19";
    }
    else {
      $century = intval($sCentury);
      if ($century < 4) {
        $result = "19";
      }
      else {
        $year = intval(substr($this->number, 4, 2));
        if ($year >= 0 && $year <= 36) {
          $result = "20";
        }
        if ($year >= 37 && $year <= 99 && ($century == 4 || $century == 9)) {
          $result = "19";
        }
        if ($year >= 58 && $year <= 99 && !($century == 4 || $century == 9)) {
          $result = "18";
        }
      }
    }
    if (strlen($result) == 0) {
      throw new CprException(__FILE__, __LINE__);
    }
    return $result;
  }
  
  public function __toString() {
    return $this->number;
  }
  
  public static function parse($input) {
    if (is_null($input)) {
      return null;
    }
    
    $prefix = "";
    if (strlen($input) == 9) {
      $prefix = "0";
    }
    $result = null;
    try {
      $result = new Cpr($prefix.$input);
    }
    catch (IllegalArgumentException $e) {
      // Ignore
    }
    return $result;
  }
}