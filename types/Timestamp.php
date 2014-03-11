<?php

class Timestamp extends Date {

  public function __construct($datetime = null) {
    parent::__construct($datetime);
  }
  
  public function diff(Date $other) {
    return $this->date->getTimestamp() - $other->date->getTimestamp();
  }
  
  public function __get($name) {
    $fmt = "";
    switch ($name) {
      case "hour" :
        $fmt = "H";
        break;
      case "minute" :
        $fmt = "i";
        break;
      case "second" :
        $fmt = "s";
        break;
      default:
        return parent::__get($name);
    }
    return intval($this->format($fmt));    
  }

  public function __set($name, $value) {
    $hour = $this->hour;
    $minute = $this->minute;
    $second = $this->second;
    switch ($name) {
      case "hour":
        $this->date->setTime(intval($value), $minute, $second);
        break;
      case "minute":
        $this->date->setTime($hour, intval($value), $second);
        break;
      case "second":
        $this->date->setTime($hour, $minute, intval($value));
        break;
      default:
        parent::__set($name, $value);
    }
  }

  protected function hasTime() {
    return true;
  }
}

?>