<?php

class AddSample extends Ruckusing_Migration_Base {

	private $table = "sample";
	
	public function up() {
    $t = $this->create_table($this->table, array("id" => false, 'options' => 'Engine=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci'));
    $t->column("uid", "primary_key", array("primary_key" => true, "auto_increment" => true, "unsigned" => true, "null" => false));
    $t->column("case_number", "integer");
    $t->column("date_value", "date");
    $t->column("cpr", "integer");
    $t->column("int_value", "integer");
    $t->column("string_value", "string", array("limit" => 45));
    $t->column("decimal_value", "decimal", array("precision" => 10, "scale" => 2));
    $t->column("boolean_value", "integer");
    $t->finish();
	}

	public function down() {
    $this->drop_table($this->table);
	}
}
?>