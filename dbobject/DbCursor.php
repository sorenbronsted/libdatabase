<?php
namespace sbronsted;

use PDO;
use PDOStatement;

class DbCursor {
	private $current = null;
	private $stmt = null;

	public function __construct(PDOStatement $stmt) {
		$this->stmt = $stmt;
		$this->current = $this->stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function hasNext() : bool {
		return $this->current != false;
	}

	public function next() : array {
		$retval = $this->current;
		$this->current = $this->stmt->fetch(PDO::FETCH_ASSOC);
		return $retval;
	}
}
