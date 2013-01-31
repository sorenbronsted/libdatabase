<?php

class Db {
  public static function buildWhere($qbe) {
    $where = "";
    foreach ($qbe as $name => $value) {
      if ($value !== null) {
        if ($where) {
          $where .= " and ";
        }
        $where .= "$name = ?";
      }
    }
    if ($where) {
      $where = "where $where";
    }
    return $where;
  }

  public static function buildSelect($table, $qbe, $orderby = array()) {
    $where = self::buildWhere($qbe);
    $orderby_s = "";
    if ($orderby) {
      $orderby_s = " order by ".implode(',', $orderby);
    }
    return "select * from ".strtolower($table)." $where $orderby_s";
  }

  public static function buildValues($qbe) {
    return array_values($qbe);  
  }
  
  public static function select($table, $qbe) {
    $sql = self::buildSelect($table, $qbe);
    $values = self::buildValues($qbe);
    return self::prepareQuery($sql, $values);
  }

  public static function update(DbObject $object) {
    $data = $object->getChanged();
    $list = "";
    $values = array();
    foreach ($data as $name) {
      $value = $object->$name;
      if ($value !== null) {
        if ($list) {
          $list .= ", ";
        }
        $list .= "$name = ?";
        $values[] = $value;
      }
    }
    if ($list) {
      $sql = "update ".strtolower(get_class($object))." set $list where uid = $object->uid";
      self::prepareExec($sql, $values);
    }
  }

  public static function insert(DbObject $object) {
    $data = $object->getData();
    $columns = "";
    $placeHolders = "";
    $values = array();
    foreach ($data as $name => $value) {
      if ($name == "uid") {
        continue;
      }
      if ($value !== null) {
        if (strlen($columns) > 0) {
          $columns .= ',';
          $placeHolders .= ',';
        }
        $columns .= $name;
        $values[] = $value;
        $placeHolders .= '?';
      }
    }
    $sql = "insert into ".strtolower(get_class($object))."($columns) values($placeHolders)";
    $object->uid = self::prepareExec($sql, $values);
  }

  public static function delete(DbObject $object) {
    self::deleteBy(get_class($object), array("uid" => $object->uid));
  }

  public static function deleteBy($table, array $qbe) {
    if (!$qbe) {
      return;
    }
    $where = self::buildWhere($qbe);
    self::deleteWhere($table, $where, $qbe);
  }

  public static function deleteWhere($table, $where, $qbe = null) {
    if (!$where) {
      return;
    }
    $sql = "delete from ".strtolower($table). " $where";
    if ($qbe == null) {
      self::exec($sql);
    }
    else {
      self::prepareExec($sql, array_values($qbe));
    }
  }

  public static function prepareExec($sql, $values) {
    if (Config::dbDebug) {
      syslog(LOG_DEBUG, "sql: $sql");
      syslog(LOG_DEBUG, "values: ".implode(';', $values));
    }
    $db = DbFactory::getConnect();
    $stmt = $db->prepare($sql);
    if ($stmt === false) {
      throw new RuntimeException("prepare statement failed");
    }
    $stmt->execute($values);
    return $db->lastInsertId();
  }

  public static function prepareQuery($sql, $values) {
    if (Config::dbDebug) {
      syslog(LOG_DEBUG, "sql: $sql");
      syslog(LOG_DEBUG, "values: ".implode(';', $values));
    }
    $db = DbFactory::getConnect();
    $stmt = $db->prepare($sql);
    if ($stmt === false) {
      throw new RuntimeException("prepare statement failed");
    }
    $stmt->execute($values);
    return new DbCursor($stmt);
  }

  public static function exec($sql) {
    if (Config::dbDebug) {
      syslog(LOG_DEBUG, $sql);
    }
    $db = DbFactory::getConnect();
    $db->exec($sql);
    return $db->lastInsertId();
  }

  public static function query($sql) {
    if (Config::dbDebug) {
      syslog(LOG_DEBUG, $sql);
    }
    $db = DbFactory::getConnect();
    return new DbCursor($db->query($sql));
  }
  
  public static function dbtype() {
    $db = DbFactory::getConnect();
    $type = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
    syslog(LOG_DEBUG, "database: $type");
    return $type;
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