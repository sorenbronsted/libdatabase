<?php

abstract class DbObject {
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
		return $this->data[$name];
	}
	
	public function __set($name, $value) {
	  $properties = $this->getProperties();
		$this->data[$name] = Property::getValue($properties[$name], $value);
		if ($name != "uid") {
			$this->changed[] = $name;
		}
	}
	
  public function setData(array $data) {
	  $properties = $this->getProperties();
	  foreach (array_keys($properties) as $name) {
	    if (array_key_exists($name, $data)) {
	     $this->$name = $data[$name];
	    }
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
      Db::update($this);
    }
    else {
      Db::insert($this);
    }
		$this->changed = array();
  }
  
  public function destroy() {
    Db::delete($this);
  }

  public function destroyBy(array $where) {
    Db::deleteBy(get_called_class(), $where);
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
    $result = self::get($where);
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

  public static function get($qbe = array(), $orderby = array()) {
    $sql = Db::buildSelect(get_called_class(), $qbe, $orderby);
    return self::getObjects($sql);
  }

	public static function getWhere($where) {
		$class = strtolower(get_called_class());
		$sql = "select * from $class where $where";
		return self::getObjects($sql);
	}
	
  public static function getObjects($sql) {
    $result = array();
		$class = get_called_class();
    $cursor = Db::query($sql);
    while ($cursor->hasNext()) {
      $row = $cursor->next();
      $object = new $class($row);
      $result[] = $object;
    }
    return $result;
  }
	
}