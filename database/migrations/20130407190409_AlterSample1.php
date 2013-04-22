<?php

class AlterSample1 extends Ruckusing_BaseMigration {

	private $table = "sample";

	public function up() {
		$this->add_column($this->table, "datetime_value", "datetime");
	}

	public function down() {
		$this->remove_column($this->table, "datetime_value");
	}
}
?>