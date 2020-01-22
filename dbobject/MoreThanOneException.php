<?php
namespace sbronsted;

use ErrorException;

class MoreThanOneException extends ErrorException {
  public function __construct(string $class, string $filename, int $lineno) {
    parent::__construct("Query for $class returned more than one object", 0, 0, $filename, $lineno);
  }
}
