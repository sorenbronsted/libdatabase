<?php

class AlterSample1 extends Ruckusing_Migration_Base {

	private $table = "samplemysql";

	public function up() {
		$this->add_column($this->table, "datetime_value", "datetime");
	}

	public function down() {
		$this->remove_column($this->table, "datetime_value");
	}
}
