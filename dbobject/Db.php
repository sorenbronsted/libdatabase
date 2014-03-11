<?php

class Db {
	
	/* Builds a name = ?, ... list to be used with update
	 * return: an array values from qbe
	 */
	public static function buildSetList($qbe) {
		return implode(',', self::buildAssigments($qbe));
	}
	
	/* Builds a name = ? and ... list to be used with where
	 */
	public static function buildConditionList($qbe) {
		return implode(' and ', self::buildAssigments($qbe));
	}
	
	/* Builds a name, ... list which can used with select or insert
	 */
	public static function buildNameList($qbe) {
		return implode(',', array_keys($qbe));
	}
	
	/* Builds a value, ... list which can used with values in insert
	 */
	public static function buildValueList($qbe) {
		return implode(',', array_values($qbe));
	}

	/* Builds a value = ?,... list
	 */
	public static function buildAssigments($qbe) {
		$assigments = array_keys($qbe);
		for ($i = 0; $i < count($assigments); $i++) {
			$assigments[$i] = $assigments[$i]." = ?";
		}
		return $assigments;
	}
	
  public static function buildSelect($table, $qbe, $orderby = array()) {
    $where = self::buildConditionList($qbe);
    $orderby_s = "";
    if (count($orderby) > 0) {
			var_dump($orderby);
      $orderby_s = " order by ".implode(',', $orderby);
    }
    return "select * from ".strtolower($table)." where $where $orderby_s";
  }

  public static function select($table, $qbe, $orderby = array(), $dbName) {
    $sql = self::buildSelect($table, $qbe, $orderby);
    $values = self::buildValueList($qbe);
    return self::prepareQuery($dbName, $sql, $values);
  }

  public static function deleteBy($table, array $qbe, $dbName) {
    if (!$qbe) {
      return;
    }
    $where = self::buildConditionList($qbe);
    self::deleteWhere($table, $where, $qbe, $dbName);
  }

  public static function deleteWhere($table, $where, $qbe, $dbName) {
    if (!$where) {
      return;
    }
    $sql = "delete from ".strtolower($table). " where $where";
    if ($qbe == null) {
      self::exec($sql);
    }
    else {
      self::prepareExec($dbName, $sql, array_values($qbe));
    }
  }

  public static function prepareExec($dbName, $sql, $values = array()) {
		$log = DiContainer::instance()->log;
		if ($log != null) {
			$log->debug(__CLASS__, "$dbName: $sql");
			$log->debug(__CLASS__, "$dbName: ".implode(';', $values));
    }
    $db = DbFactory::getConnection($dbName);
    $stmt = $db->prepare($sql);
    if ($stmt === false) {
      throw new RuntimeException("prepare statement failed");
    }
    $stmt->execute($values);
    return $db->lastInsertId();
  }

  public static function prepareQuery($dbName, $sql, $values) {
		$log = DiContainer::instance()->log;
		if ($log != null) {
			$log->debug(__CLASS__, "$dbName: $sql");
			$log->debug(__CLASS__, "$dbName: ".implode(';', $values));
    }
    $db = DbFactory::getConnection($dbName);
    $stmt = $db->prepare($sql);
    if ($stmt === false) {
      throw new RuntimeException("prepare statement failed");
    }
    $stmt->execute($values);
    return new DbCursor($stmt);
  }

  public static function exec($dbName, $sql) {
		$log = DiContainer::instance()->log;
		if ($log != null) {
			$log->debug(__CLASS__, "$dbName: $sql");
    }
    $db = DbFactory::getConnection($dbName);
    $db->exec($sql);
    return $db->lastInsertId();
  }

  public static function query($dbName, $sql) {
		$log = DiContainer::instance()->log;
		if ($log != null) {
			$log->debug(__CLASS__, "$dbName: $sql");
    }
    $db = DbFactory::getConnection($dbName);
    return new DbCursor($db->query($sql));
  }
}

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