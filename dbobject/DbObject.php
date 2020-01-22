<?php
namespace sbronsted;

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

	public function __get(string $name) {
		return (array_key_exists($name, $this->data) ? $this->data[$name] : null);
	}
	
	public function __set(string $name, $value) : void {
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

	public function __isset(string $name) : bool {
		return isset($this->data[$name]);
	}

	public function setData(array $data) : void {
		foreach (array_keys($data) as $name) {
			$this->$name = $data[$name]; // This will trigger __set
		}
	}

	public function getChanged() : array {
		return $this->changed;
	}
	
	public function hasFieldChanged(string $name) : bool {
		return array_search($name, $this->changed) !== false;
	}
	
	public function getData() : array {
		return $this->data;
	}
	
	protected abstract function getProperties() : array;
	
  public function save() : void {
    if ($this->uid) {
      $this->update();
    }
    else {
      $this->insert();
    }
		$this->changed = array();
  }
  
  public function update() : void {
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

  public function insert() : void {
    $data = $this->getData();
		unset($data['uid']);
		$columns = Db::buildNameList($data);
		$values = array_values($data);
		$placeHolders = implode(',', array_fill(0, count(array_keys($data)), '?'));
    $sql = "insert into ".strtolower($this->getClass())."($columns) values($placeHolders)";
    $this->uid = Db::prepareExec(static::$db, $sql, $values);
  }

  public function destroy() : void {
    Db::deleteBy($this->getClass(), array("uid" => $this->uid), static::$db);
  }

  public static function destroyBy(array $where) : void {
    Db::deleteBy(self::getCalledClass(), $where, static::$db);
  }

  public static function getAll(array $orderby = array()) : iterable {
    return self::get(array(), $orderby);
  }

  public static function getByUid(int $uid) : object {
    return self::getOneBy(array("uid" => $uid));
  }

  public static function getBy(array $where, array $orderby = array()) : iterable {
    return self::get($where, $orderby);
  }

  public static function getOneBy(array $where) : object {
    $result = self::get($where, array());
		return self::verifyOne($result);
  }

  public static function verifyOne(iterable $result) : object {
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

  public static function get(array $qbe = array(), array $orderby = array()) : iterable {
    $sql = Db::buildSelect(strtolower(self::getCalledClass()), $qbe, $orderby);
    return self::getObjects($sql, $qbe);
  }

	public static function getWhere(string $where, array $qbe = null) : iterable {
		$class = strtolower(self::getCalledClass());
		$sql = "select * from $class where $where";
		return self::getObjects($sql, $qbe);
	}
	
  public static function getObjects(string $sql, $qbe = null) : iterable{
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

  public static function getCalledClass() : string {
	  $class = get_called_class();
	  return self::getClassName($class);
  }

  public function getClass() : string {
  	$class = get_class($this);
	  return self::getClassName($class);
  }

	protected static function getClassName($class) : string {
  	$parts = explode('\\', $class);
  	return $parts[1];
	}
}