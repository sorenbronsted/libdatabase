<?php
namespace sbronsted;

use RuntimeException;

class Db {
	
	/* Builds a name = ?, ... list to be used with update
	 * return: an array values from qbe
	 */
	public static function buildSetList(array $qbe) : string {
		return implode(',', self::buildAssigments($qbe));
	}
	
	/* Builds a name = ? and ... list to be used with where
	 */
	public static function buildConditionList(array $qbe) : string {
		return implode(' and ', self::buildExpression($qbe));
	}
	
	/* Builds a name, ... list which can used with select or insert
	 */
	public static function buildNameList(array $qbe) : string {
		return implode(',', array_keys($qbe));
	}
	
	/* Builds a value = ?,... list
	 */
	public static function buildAssigments(array $qbe) : array {
		$assigments = array_keys($qbe);
		for ($i = 0; $i < count($assigments); $i++) {
			$assigments[$i] = $assigments[$i]." = ?";
		}
		return $assigments;
	}

	/* Builds a value op ?,... list where op can be =, is and like
	 */
	public static function buildExpression(array $qbe) : array {
		$assigments = array();
		foreach($qbe as $name => $value) {
			$op = '=';
			if ($value === null) {
				$op  = 'is';
			}
			else if (strpos($value, '%') !== false) {
				$op = 'like';
			}
			$assigments[] = "$name $op ?";
		}
		return $assigments;
	}

	public static function buildSelect(string $table, array $qbe, array $orderby = array()) : string {
    $where = self::buildConditionList($qbe);
		if (strlen($where) > 0) {
			$where = ' where '.$where;
		}
    $sOrderby = "";
    if (count($orderby) > 0) {
      $sOrderby = " order by ".implode(',', $orderby);
    }
    return "select * from ".strtolower($table)." $where $sOrderby";
  }

  public static function select(string $table, array $qbe, string $dbName, array $orderby = array()) : DbCursor {
    $sql = self::buildSelect($table, $qbe, $orderby);
    return self::prepareQuery($dbName, $sql, array_values($qbe));
  }

  public static function deleteBy(string $table, array $qbe, string $dbName) : void {
    if (!$qbe) {
      return;
    }
    $where = self::buildConditionList($qbe);
    self::deleteWhere($table, $where, $qbe, $dbName);
  }

  public static function deleteWhere(string $table, string $where, array $qbe, string $dbName) : void {
    if (!$where) {
      return;
    }
    $sql = "delete from ".strtolower($table). " where $where";
    if ($qbe == null) {
      self::exec($dbName, $sql);
    }
    else {
      self::prepareExec($dbName, $sql, array_values($qbe));
    }
  }

  public static function prepareExec(string $dbName, string $sql, array $values = array()) : int {
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

  public static function prepareQuery(string $dbName, string $sql, array $values = array()) : DbCursor {
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

  public static function exec(string $dbName, string $sql) : int {
		$log = DiContainer::instance()->log;
		if ($log != null) {
			$log->debug(__CLASS__, "$dbName: $sql");
    }
    $db = DbFactory::getConnection($dbName);
    $db->exec($sql);
    return $db->lastInsertId();
  }

  public static function query(string $dbName, string $sql) : DbCursor {
		$log = DiContainer::instance()->log;
		if ($log != null) {
			$log->debug(__CLASS__, "$dbName: $sql");
    }
    $db = DbFactory::getConnection($dbName);
    return new DbCursor($db->query($sql));
  }
}
