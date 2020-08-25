<?php
namespace sbronsted;

/**
 * Class DbObject provides convenience method mapping this object to CRUD sql operations. By convention the derived
 * classes must have a property named uid. By declaring which properties this class has, these can modified by
 * get and set magic methods.
 */
abstract class DbObject {
	public static $db = 'defaultDb';
	private $data = array();
	private $changed = array();

	public function __construct($data = []) {
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

	/**
	 * Get a property
	 * @param string $name
	 * 	The name of the property
	 * @return mixed|null
	 * 	If found it returns the value otherwise null
	 */
	public function __get(string $name) {
		return (array_key_exists($name, $this->data) ? $this->data[$name] : null);
	}

	/**
	 * Set property
	 * @param string $name
	 * 	The name of the property
	 * @param $value
	 * 	The value to set
	 */
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
		else if (property_exists($this, $name) && !in_array($name, ['changed', 'data'])) {
			$this->$name = $value;
		}
		// Silently ignore unknown properties
	}

	/**
	 * @see php isset
	 * @param string $name
	 * @return bool
	 */
	public function __isset(string $name) : bool {
		return isset($this->data[$name]);
	}

	/**
	 * Set all properties.
	 * @param array $data
	 * 	The data values where keys are properties names
	 */
	public function setData(array $data) : void {
		foreach (array_keys($data) as $name) {
			$this->__set($name, $data[$name]);
		}
	}

	/**
	 * Get changed properties
	 * @return array
	 * 	The changed properties
	 */
	public function getChanged() : array {
		return $this->changed;
	}

	/**
	 * Test is a property has changed
	 * @param string $name
	 * 	The property to test
	 * @return bool
	 * 	Returns true ff found and is change otherwise false
	 */
	public function hasFieldChanged(string $name) : bool {
		return array_search($name, $this->changed) !== false;
	}

	/**
	 * Get all properties and values
	 * @return array
	 * 	The properties and values
	 */
	public function getData() : array {
		return $this->data;
	}

	/**
	 * Get all defined properties and their types for this class
	 * @return array
	 * 	The properties and type information
	 */
	protected abstract function getProperties() : array;

	/**
	 * Save the this object. If uid == 0 it is created otherwise it is updated
	 */
  public function save() : void {
    if ($this->uid) {
      $this->update();
    }
    else {
      $this->insert();
    }
		$this->changed = array();
  }
  
  protected function update() : void {
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

	protected function insert() : void {
    $data = $this->getData();
		unset($data['uid']);
		$columns = Db::buildNameList($data);
		$values = array_values($data);
		$placeHolders = implode(',', array_fill(0, count(array_keys($data)), '?'));
    $sql = "insert into ".strtolower($this->getClass())."($columns) values($placeHolders)";
    $this->uid = Db::prepareExec(static::$db, $sql, $values);
  }

	/**
	 * Delete this object
	 */
  public function destroy() : void {
    Db::deleteBy($this->getClass(), array("uid" => $this->uid), static::$db);
  }

	/**
	 * Delete objects by matching all properties and values
	 * @param array $where
	 * 	The properties and values
	 */
  public static function destroyBy(array $where) : void {
    Db::deleteBy(self::getCalledClass(), $where, static::$db);
  }

	/**
	 * Get objects of this class
	 * @param array $orderby
	 *  The properties to order by
	 * @return array
	 *  The result
	 * @throws ConnectionException
	 */
  public static function getAll(array $orderby = array()) : array {
    return self::get(array(), $orderby);
  }

	/**
	 * Get an object by uid
	 * @param int $uid
	 *  The uid to lookup
	 * @return object
	 *  The result
	 * @throws ConnectionException
	 * @throws MoreThanOneException
	 * @throws NotFoundException
	 */
  public static function getByUid(int $uid) : object {
    return self::getOneBy(array("uid" => $uid));
  }

	/**
	 * Get objects by matching all properties
	 * @param array $where
	 *  The properties and values to match
	 * @param array $orderby
	 *  The properties to order by
	 * @return array
	 *  The result
	 * @throws ConnectionException
	 */
  public static function getBy(array $where, array $orderby = array()) : array {
    return self::get($where, $orderby);
  }

	/**
	 * Get one object by matching all properties
	 * @param array $where
	 *  The properties and values to match
	 * @return object
	 *  The reusult
	 * @throws MoreThanOneException
	 * @throws NotFoundException
	 * @throws ConnectionException
	 */
  public static function getOneBy(array $where) : object {
    $result = self::get($where, array());
		return self::verifyOne($result);
  }

	/**
	 * Verify that result only contains one object and return it if true
	 * @param array $result
	 * 	The object to test
	 * @return object
	 * 	The object if only one
	 * @throws MoreThanOneException
	 * @throws NotFoundException
	 */
  public static function verifyOne(array $result) : object {
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

	/**
	 * Get objects by matching all property values
	 * @param array $qbe
	 *  The properties and values to match
	 * @param array $orderby
	 *  The properties t order by
	 * @return array
	 *  The result
	 * @throws ConnectionException
	 */
  public static function get(array $qbe = array(), array $orderby = array()) : array {
    $sql = Db::buildSelect(strtolower(self::getCalledClass()), $qbe, $orderby);
    return self::getObjects($sql, $qbe);
  }

	/**
	 * Get objects by match where expression containing placeholders
	 * @param string $where
	 *  The where expression
	 * @param array|null $qbe
	 *  The placeholder values
	 * @return array
	 *  The result
	 * @throws ConnectionException
	 */
	public static function getWhere(string $where, array $qbe = null) : array {
		$class = strtolower(self::getCalledClass());
		$sql = "select * from $class where $where";
		return self::getObjects($sql, $qbe);
	}

	/**
	 * Get objects by a query sql statement with placeholders
	 * @param string $sql
	 *  The query sql statement
	 * @param array $qbe
	 * 	The placeholder values
	 * @return array
	 * 	The result
	 * @throws ConnectionException
	 */
  public static function getObjects(string $sql, array $qbe = null) : array{
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

	/**
	 * Get the class name without namespace
	 * @return string
	 * 	The class name
	 */
  public static function getCalledClass() : string {
	  $class = get_called_class();
	  return self::getClassName($class);
  }

	/**
	 * Get the class name without namespace
	 * @return string
	 * 	The class name
	 */
  public function getClass() : string {
  	$class = get_class($this);
	  return self::getClassName($class);
  }

	/**
	 * Get the class name without namespace
	 * @param $class
	 * 	The class name with namespace
	 * @return string
	 * 	The class name without namespace
	 */
	protected static function getClassName($class) : string {
  	$parts = explode('\\', $class);
  	return $parts[1];
	}
}