<?php
class CaseNumberException extends ErrorException {
  public function __construct($faultValue, $file, $line) {
    parent::__construct("CaseNumber '$faultValue' is not valid ($file,$line)");
  }
}
?>
