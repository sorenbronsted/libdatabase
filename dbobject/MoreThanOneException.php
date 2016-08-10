<?php
namespace ufds;

use ErrorException;

class MoreThanOneException extends ErrorException {
  public function __construct($class, $filename, $lineno) {
    parent::__construct("Query for $class returned more than one object", 0, 0, $filename, $lineno);
  }
}
