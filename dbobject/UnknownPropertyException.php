<?php
namespace sbronsted;

use ErrorException;

class UnknownPropertyException extends ErrorException {
	public function __construct(string $name, string $filename, int $lineno) {
		parent::__construct("$name is unknown", 0, 0, $filename, $lineno);
	}
}
