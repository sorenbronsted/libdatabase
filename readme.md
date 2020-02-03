#Libdatabase

This is a simple ORM database mapper which delivers some convenience methods to query, create and update objects.
It does not support associations. Instead I use the convention that a foreign key is the name of the table appended
'_uid'. The only other convention is that an object must have a property named 'uid' and when you call save 
and the uid == 0 it will be converted to an insert statement otherwise it will be converted to an update statement.

This library uses php PDO library.

## Example

Below is defined a Sample class

```  
class Sample extends DbObject {
  
  // Each object must describe the properties of the table
  private static $properties = [
    'uid'            => Property::INT,
    'case_number'    => Property::CASE_NUMBER,
    'date_value'     => Property::DATE,
    'datetime_value' => Property::TIMESTAMP,
    'cpr'            => Property::CPR,
    'int_value'      => Property::INT,
    'string_value'   => Property::STRING,
    'decimal_value'  => Property::DECIMAL,
    'boolean_value'  => Property::BOOLEAN
  ];

  // A transient value which is not persisted
  public $transient_value;

  // mandatory method used by DbObject
  public function getProperties() : array {
    return self::$properties;
  }
}
```  

This class will have a corresponding table name sample where properties are columns.

As you se the properties are describe by types, which means that when the object is loaded from a table row, 
the columns are converted to the property type. In that way the date is not a string, but an date object with 
meaningful operation.

To create an object and persist it
```
$sample = new Sample();
$uid = $sample->save();    
```  

To to find one or more objects
```
// returns a single object
$sample = Sample::getByUid($uid);

// returns 0 or more objects
$samples = Sample::getBy(['int_value' => 1]);
```  

To change a property and persist it
```
$sample = Sample::getByUid($uid);
$sample->int_value = 1;
$sample->save();  
```  

To delete an object
```  
$sample = Sample::getByUid($uid);
$sample->destroy();
```  

You also have access to the lower level database function in the DB class, which provides 
transformations for DbObject to sql or you can write the sql your self. 

## Configuration

This library uses the Config class from [libutil](https://github.com/sorenbronsted/libutil), 
where you can put in the database configuration into the ini file:
```  
[defaultDb]
driver=mysql
host=localhost
port=3306
name=yourDatabase
user=yourUserName
password=yourUserPassword
charset=utf8
```  

The section name corresponds to the static property $db in the DbObject class. The default name is 'defaultDb'.
