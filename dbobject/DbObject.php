<?php
namespace ufds;

abstract class DbObject {
	public static $db = 'defaultDb';
	private $data = array();
	private $changed = array();

	public function __construct($data = array()) {
    $properties = $this->getProperties();
		// Ensure that all keys are lowercase
		$modified = array();
		foreach ($data as $k => $v) {
			$k = strtolower($k);
			$modified[$k] = $v;
		}
    foreach ($properties as $name => $type) {
      $this->data[$name] = (isset($modified[$name]) ? Property::getValue($type, $modified[$name]) : null);
    }
		if (empty($data)) {
			$this->data["uid"] = 0;
		}
	}

	public function __get($name) {
		return (array_key_exists($name, $this->data) ? $this->data[$name] : null);
	}
	
	public function __set($name, $value) {
	  $properties = $this->getProperties();

		if (array_key_exists($name, $properties)) {
			$newValue = Property::getValue($properties[$name], $value);
			// If new and existing value are the same no assignment is needed
			if (Property::isEqual($properties[$name], $this->data[$name], $newValue)) {
				return;
			}
			$this->data[$name] = $newValue;
			if ($name != "uid" && !$this->hasFieldChanged($name)) {
				$this->changed[] = $name;
			}
		}
		else if (property_exists($this, $name)) {
			$this->$name = $value;
		}
		// Silently ignore unknown properties
	}

	public function __isset($name) {
		return isset($this->data[$name]);
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
	
  public function save() {
    if ($this->uid) {
      $this->update();
    }
    else {
      $this->insert();
    }
		$this->changed = array();
  }
  
  public function update() {
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
	  $qbe['uid'] = $this->uid;
	  $sql = "update ".strtolower($this->getClass())." set $list where uid = ?";
	  Db::prepareExec(static::$db, $sql, array_values($qbe));
  }

  public function insert() {
    $data = $this->getData();
		unset($data['uid']);
		$columns = Db::buildNameList($data);
		$values = array_values($data);
		$placeHolders = implode(',', array_fill(0, count(array_keys($data)), '?'));
    $sql = "insert into ".strtolower($this->getClass())."($columns) values($placeHolders)";
    $this->uid = Db::prepareExec(static::$db, $sql, $values);
  }

  public function destroy() {
    Db::deleteBy($this->getClass(), array("uid" => $this->uid), static::$db);
  }

  public static function destroyBy(array $where) {
    Db::deleteBy(self::getCalledClass(), $where, static::$db);
  }

  public static function getAll(array $orderby = array()) {
    return self::get(array(), $orderby);
  }

  public static function getByUid($uid) {
    return self::getOneBy(array("uid" => $uid));
  }

  public static function getBy(array $where, array $orderby = array()) {
    return self::get($where, $orderby);
  }

  public static function getOneBy(array $where) {
    $result = self::get($where, array());
		return self::verifyOne($result);
  }

  public static function verifyOne(array $result) {
		if (count($result) == 1) {
			return $result[0];
		}
    if (count($result) > 1) {
      throw new MoreThanOneException(self::getCalledClass(), __FILE__, __LINE__);
    }
		if (count($result) == 0) {
			throw new NotFoundException(self::getCalledClass(), __FILE__, __LINE__);
		}
  }

  public static function get($qbe = array(), $orderby = array()) {
    $sql = Db::buildSelect(strtolower(self::getCalledClass()), $qbe, $orderby);
    return self::getObjects($sql, $qbe);
  }

	public static function getWhere($where, $qbe = null) {
		$class = strtolower(self::getCalledClass());
		$sql = "select * from $class where $where";
		return self::getObjects($sql, $qbe);
	}
	
  public static function getObjects($sql, $qbe = null) {
    $result = array();
		$class = get_called_class();
		$cursor = null;
		if ($qbe != null) {
			if (strpos($sql, ':') === false ) {
				$qbe = array_values($qbe);
			}
			$cursor = Db::prepareQuery(static::$db, $sql, $qbe);
		}
		else {
			$cursor = Db::query(static::$db, $sql);
		}
    while ($cursor->hasNext()) {
      $row = $cursor->next();
      $object = new $class($row);
      $result[] = $object;
    }
    return $result;
  }

  public static function getCalledClass() {
	  $class = get_called_class();
	  return self::getClassName($class);
  }

  public function getClass() {
  	$class = get_class($this);
	  return self::getClassName($class);
  }

	protected static function getClassName($class) {
		$start = strpos($class, '\\');
		if ($start === false) {
			$start = 0;
		}
		else {
			$start += 1;
		}
		return substr($class, $start);
	}
}