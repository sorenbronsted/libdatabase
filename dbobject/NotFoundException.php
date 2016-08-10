<?php
namespace ufds;

use ErrorException;

class NotFoundException extends ErrorException {
	public function __construct($class, $filename, $lineno) {
		parent::__construct("$class not found", 0, 0, $filename, $lineno);
	}
}
