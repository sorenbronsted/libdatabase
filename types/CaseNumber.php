<?php

class CaseNumber {
  private $number;

  public function __construct($number) {
    $this->number = $number;
    $this->toInt();
  }

  public function __toString() {
    return strval($this->number);
  }

  public function toString() {
    return $this->__toString();
  }

  public function isEqual(CaseNumber $cn) {
    return $this->number == $cn->toNumber();
  }
  
  public function toNumber() {
    return $this->number;
  }
  
  private function toInt() {
    if (!is_string($this->number)) {
      return;
    }
    
    if (strpos($this->number, "/")) {
      $caseNumber = explode("/", $this->number);
      if (strlen($caseNumber[1]) >= 1 && strlen($caseNumber[1]) <= 2) {
        $century = "19";
        if ($caseNumber[1] < 70) {
          $century = "20";
        }
        $caseNumber[1] = $century . sprintf("%02d", $caseNumber[1]);
      }
      else {
        if (strlen($caseNumber[1]) != 4) {
          throw new CaseNumberException($this->number, __FILE__,__LINE__);
        }
      }
      $this->number = intval($caseNumber[1]) * 10000 + intval(sprintf("%04d", $caseNumber[0]));
    }
    else {
      $this->number = intval($this->number);
    }
    
    if ($this->number < 19000000) {
      throw new CaseNumberException($this->number, __FILE__,__LINE__);
    }
  }
}
  