<?php

class AlterSample2 extends Ruckusing_Migration_Base {

	private $table = "samplemysql";

	public function up() {
		$this->add_column($this->table, "changed", "string");
	}

	public function down() {
		$this->remove_column($this->table, "changed");
	}
}
