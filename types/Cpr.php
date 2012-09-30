<?php

class Cpr {
  private $number;
  
  public function __construct($number) {
    $prefix = "";
    if (strlen($number) == 9) {
      $prefix = "0";
    }
    $this->number = $prefix.$number;
    if (strlen($this->number) != 10) {
      throw new CprException(__FILE__, __LINE__);
    }
  }
  
  public function getDate() {
    $century = $this->getCentury();
    return new Date(substr($this->number,0,6).$century, "%d%m%y%C");
  }
  
  public function getAgeAt($date) {
    if (is_string($date)) {
      $date = new Date($date, "%Y%m%d");
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
}