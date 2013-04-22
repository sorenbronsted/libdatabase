<?php

class Timestamp extends Date {

  public function __construct($datetime = null) {
    parent::__construct($datetime);
  }
  
  protected function hasTime() {
    return true;
  }
}

?>