<?php
namespace sbronsted;

use ErrorException;

class ConnectionException extends ErrorException {
	public function __construct(string $msg, string $filename, int $lineno) {
		parent::__construct($msg, 0, 0, $filename, $lineno);
	}
}
