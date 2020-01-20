<?php
namespace sbronsted;

use ErrorException;

class ConnectionException extends ErrorException {
	public function __construct($msg, $filename, $lineno) {
		parent::__construct($msg, 0, 0, $filename, $lineno);
	}
}
