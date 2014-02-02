<?php

class Timestamp extends Date {

  public function __construct($datetime = null) {
    parent::__construct($datetime);
  }
  
  public function diff(Date $other) {
    return $this->date->getTimestamp() - $other->date->getTimestamp();
  }
  
  protected function hasTime() {
    return true;
  }
}

?>