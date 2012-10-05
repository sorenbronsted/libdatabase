<?php

class Db {
  public static function buildWhere($qbe) {
    $where = "";
    foreach ($qbe as $name => $value) {
      if ($value !== null) {
        if ($where) {
          $where .= " and ";
        }
        if (is_string($value)) {
          $value = "'".$value."'";
        }
        $where .= "$name = $value";
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

  public static function select($table, $qbe) {
    $sql = self::buildSelect($table, $qbe);
    return self::query($sql);
  }

  public static function update(DbObject $object) {
    $data = $object->getChanged();
    $list = "";
    foreach ($data as $name) {
      $value = $object->$name;
      if ($value !== null) {
        if ($list) {
          $list .= ", ";
        }
        $list .= "$name = '".$value."'";
      }
    }
    if ($list) {
      $sql = "update ".strtolower(get_class($object))." set $list where uid = $object->uid";
      self::exec($sql);
    }
  }

  public static function insert(DbObject $object) {
    $data = $object->getData();
    $columns = "";
    $values = "";
    foreach ($data as $name => $value) {
      if ($value !== null) {
        if (strlen($columns) > 0) {
          $columns .= ',';
          $values .= ',';
        }
        $columns .= $name;
        $values .= "'".$value."'";
      }
    }
    $sql = "insert into ".strtolower(get_class($object))."($columns) values($values)";
    $object->uid = self::exec($sql);
  }

  public static function delete(DbObject $object) {
    self::deleteBy(get_class($object), array("uid" => $object->uid));
  }

  public static function deleteBy($table, $where) {
    if (!$where) {
      return;
    }
    $where = self::buildWhere($where);
    self::deleteWhere($table, $where);
  }

  public static function deleteWhere($table, $where) {
    if (!$where) {
      return;
    }
    $sql = "delete from ".strtolower($table). " $where";
    self::exec($sql);
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