<?php

abstract class DbObject {
	const defaultDb = 'defaultDb';
	private $data = array();
	private $changed = array();

	public function __construct($data = array()) {
    $properties = $this->getProperties();
    foreach ($properties as $name => $type) {
      $this->data[$name] = (array_key_exists($name, $data) ? Property::getValue($type, $data[$name]) : null);
    }
		if (count($data) == 0) {
			$this->data["uid"] = 0;
		}
	}
	
	public function __get($name) {
		return (array_key_exists($name, $this->data) ? $this->data[$name] : null);
	}
	
	public function __set($name, $value) {
	  $properties = $this->getProperties();
		if (!array_key_exists($name, $properties)) {
			return; // Silently ignore unknown properties
		}
		$newValue = Property::getValue($properties[$name], $value);
		if (is_null($this->data[$name]) || is_null($newValue) || $this->data[$name] !== $newValue) {
			$this->data[$name] = $newValue;
			if ($name != "uid" &&	!$this->hasFieldChanged($name)) {
				$this->changed[] = $name;
			}
		}
	}
	
  public function setData(array $data) {
	  foreach (array_keys($data) as $name) {
			$this->$name = $data[$name]; // This will trigger __set
	  }
	}

	public function getChanged() {
		return $this->changed;
	}
	
	public function hasFieldChanged($name) {
		return array_search($name, $this->changed) !== false;
	}
	
	public function getData() {
		return $this->data;
	}
	
	protected abstract function getProperties();
	
  public function save($db = self::defaultDb) {
    if ($this->uid) {
      $this->update($db);
    }
    else {
      $this->insert($db);
    }
		$this->changed = array();
  }
  
  public function update($db = self::defaultDb) {
    $data = $this->getChanged();
		unset($data['uid']);
		if (count($data) <= 0) {
			return;
		}
		$qbe = array();
		foreach($data as $name) {
			$qbe[$name] = $this->$name;
		}
		$list = Db::buildSetList($qbe);
		$sql = "update ".strtolower(get_class($this))." set $list where uid = $this->uid";
		Db::prepareExec($db, $sql, array_values($qbe));
  }

  public function insert($db = self::defaultDb) {
    $data = $this->getData();
		unset($data['uid']);
		$columns = Db::buildNameList($data);
		$values = array_values($data);
		$placeHolders = implode(',', array_fill(0, count(array_keys($data)), '?'));
    $sql = "insert into ".strtolower(get_class($this))."($columns) values($placeHolders)";
    $this->uid = Db::prepareExec($db, $sql, $values);
  }

  public function destroy($db = self::defaultDb) {
    Db::deleteBy(get_class($this), array("uid" => $this->uid), $db);
  }

  public static function destroyBy(array $where, $db = self::defaultDb) {
    Db::deleteBy(get_called_class(), $where, $db);
  }

  public static function getAll(array $orderby = array(), $db = self::defaultDb) {
    return self::get(array(), $orderby, $db);
  }

  public static function getByUid($uid, $db = self::defaultDb) {
    return self::getOneBy(array("uid" => $uid), $db);
  }

  public static function getBy(array $where, array $orderby = array(), $db = self::defaultDb) {
    return self::get($where, $orderby, $db);
  }

  public static function getOneBy(array $where, $db = self::defaultDb) {
    $result = self::get($where, array(), $db);
		return self::verifyOne($result);
  }

  public static function verifyOne(array $result) {
		if (count($result) == 1) {
			return $result[0];
		}
    if (count($result) > 1) {
      throw new MoreThanOneException(get_called_class(), __FILE__, __LINE__);
    }
		if (count($result) == 0) {
			throw new NotFoundException(get_called_class(), __FILE__, __LINE__);
		}
  }

  public static function get($qbe = array(), $orderby = array(), $db = self::defaultDb) {
    $sql = Db::buildSelect(strtolower(get_called_class()), $qbe, $orderby);
    return self::getObjects($sql, $qbe, $db);
  }

	public static function getWhere($where, $db = self::defaultDb) {
		$class = strtolower(get_called_class());
		$sql = "select * from $class where $where";
		return self::getObjects($sql, null, $db);
	}
	
  public static function getObjects($sql, $qbe = null, $db = self::defaultDb) {
    $result = array();
		$class = get_called_class();
		$cursor = null;
		if ($qbe != null) {
			$cursor = Db::prepareQuery($db, $sql, array_values($qbe));
		}
		else {
			$cursor = Db::query($db, $sql);
		}
    while ($cursor->hasNext()) {
      $row = $cursor->next();
      $object = new $class($row);
      $result[] = $object;
    }
    return $result;
  }
}