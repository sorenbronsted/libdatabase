<?php
namespace sbronsted;

use ErrorException;

class NotFoundException extends ErrorException {
	public function __construct(string $class, string $filename, int $lineno) {
		parent::__construct("$class not found", 0, 0, $filename, $lineno);
	}
}
