<?php
namespace sbronsted;

use PDO;

class DbCursor {
	private $current = null;
	private $stmt = null;

	public function __construct($stmt) {
		$this->stmt = $stmt;
		$this->current = $this->stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function hasNext() {
		return $this->current != false;
	}

	public function next() {
		$retval = $this->current;
		$this->current = $this->stmt->fetch(PDO::FETCH_ASSOC);
		return $retval;
	}
}
