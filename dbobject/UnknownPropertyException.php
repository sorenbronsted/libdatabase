<?php
class UnknownPropertyException extends ErrorException {
	public function __construct($name, $filename, $lineno) {
		parent::__construct("$name is unknown", 0, 0, $filename, $lineno);
	}
}
?>