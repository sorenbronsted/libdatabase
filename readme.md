This is UFDS generel ORM database mapper used in all projects.
Sample.php in test/util is a minimal object which has automatic mapping
to a corresponding database table.

Some of the tests are dependent on af database, so you need to create this.
In the sql folder is there script for this purpose.

NULL or empty values are handled by Property::getValue() and when a property
is null or empty the property is set to that value and no object is created