This is UFDS generel ORM database mapper used in all projects.
Sample.php in test/util is a minimal object which has automatic mapping
to a corresponding database table.

Some of the tests are dependent on af database, so you need to create this.
In the sql folder is there script for this purpose.

Null or empty values are handled by Property::getValue() and when a property
is null or empty the property is set to that value and no object is created

Current the orm supports mysql, sqlite and db2. The last one need pdo_ibm pecl
module installed. Version 1.3.3 does not support php5 on ubuntu so an UFDS
packages 'pdoibm' exists on dev.ufds.lan.