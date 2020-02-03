<?php
namespace sbronsted;

use PDO;
use PDOStatement;

/**
 * Class DbCursor is a iterable list of rows
 */
class DbCursor {
	private $current = null;
	private $stmt = null;

	/**
	 * DbCursor constructor.
	 * @param PDOStatement $stmt
	 * 	The statement
	 */
	public function __construct(PDOStatement $stmt) {
		$this->stmt = $stmt;
		$this->current = $this->stmt->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Tells if the cursor is at end or not
	 * @return bool
	 * 	Is true if not at end otherwise false
	 */
	public function hasNext() : bool {
		return $this->current != false;
	}

	/**
	 * Get the next row
	 * @return array
	 * 	The next row
	 */
	public function next() : array {
		$retval = $this->current;
		$this->current = $this->stmt->fetch(PDO::FETCH_ASSOC);
		return $retval;
	}
}
