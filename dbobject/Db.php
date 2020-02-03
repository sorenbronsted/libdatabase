<?php
namespace sbronsted;

use RuntimeException;

/**
 * Class Db does the actual coversion a DbObject to sql
 */
class Db {

	/**
	 * Builds a name = ?, ... list to be used with update
	 * return: an array values from qbe
	 * @param array $qbe
	 * @return string
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

	/**
	 * Build a select * from table statement with a where from qbe parameter
	 * @param string $table
	 * 	The table name
	 * @param array $qbe
	 * 	Query by example array with names and values
	 * @param array $orderby
	 * 	Names to order by
	 * @return string
	 * 	The sql statement
	 */
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

	/**
	 * Build and execute a select statement
	 * @param string $table
	 * 	The table name
	 * @param array $qbe
	 * 	Query bt example with names and avalues
	 * @param string $dbName
	 * 	The name of the config for the database to talk to
	 * @param array $orderby
	 * 	Tne names to order by
	 * @return DbCursor
	 * 	The result of the query
	 */
  public static function select(string $table, array $qbe, string $dbName, array $orderby = array()) : DbCursor {
    $sql = self::buildSelect($table, $qbe, $orderby);
    return self::prepareQuery($dbName, $sql, array_values($qbe));
  }

	/**
	 * Builds and exceute a delete statemen, which deletes the rows matched names and values from qbe.
	 * @param string $table
	 * 	The table name
	 * @param array $qbe
	 * 	Query bt example with names and avalues
	 * @param string $dbName
	 * 	The name of the config for the database to talk to
	 */
  public static function deleteBy(string $table, array $qbe, string $dbName) : void {
    if (!$qbe) {
      return;
    }
    $where = self::buildConditionList($qbe);
    self::deleteWhere($table, $where, $qbe, $dbName);
  }

	/**
	 * Builds and exceute a delete statement, which deletes the rows matched by where.
	 * @param string $table
	 * 	The table name
	 * @param string $where
	 * 	The where condition with placeholders
	 * @param array $qbe
	 * 	Query by example with names and values for the placeholders
	 * @param string $dbName
	 * 	The name of the config for the database to talk to
	 */
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

	/**
	 * Executes the sql by prepared statement
	 * @param string $dbName
	 * 	The name of the config for the database to talk to
	 * @param string $sql
	 *	The sql statement other than select with optional placeholders
	 * @param array $values
	 * 	The values for the placeholders
	 * @return int
	 * 	The last insert id
	 * @throws ConnectionException
	 */
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

	/**
	 * Executes the sql by prepared statement
	 * @param string $dbName
	 * 	The name of the config for the database to talk to
	 * @param string $sql
	 *	The select sql statement with optional placeholders
	 * @param array $values
	 * 	The values for the placeholders
	 * @return DbCursor
	 * 	The result of the query
	 * @throws ConnectionException
	 */
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

	/**
	 * Executes the sql non prepared statement
	 * @param string $dbName
	 * 	The name of the config for the database to talk to
	 * @param string $sql
	 *	The insert,delete or update sql statement with optional placeholders
	 * @return int
	 * 	The last insert id
	 * @throws ConnectionException
	 */
  public static function exec(string $dbName, string $sql) : int {
		$log = DiContainer::instance()->log;
		if ($log != null) {
			$log->debug(__CLASS__, "$dbName: $sql");
    }
    $db = DbFactory::getConnection($dbName);
    $db->exec($sql);
    return $db->lastInsertId();
  }

	/**
	 * Executes the sql non prepared statement
	 * @param string $dbName
	 * 	The name of the config for the database to talk to
	 * @param string $sql
	 *	The select sql statement with optional placeholders
	 * @return DbCursor
	 * 	The query result
	 * @throws ConnectionException
	 */
  public static function query(string $dbName, string $sql) : DbCursor {
		$log = DiContainer::instance()->log;
		if ($log != null) {
			$log->debug(__CLASS__, "$dbName: $sql");
    }
    $db = DbFactory::getConnection($dbName);
    return new DbCursor($db->query($sql));
  }
}
