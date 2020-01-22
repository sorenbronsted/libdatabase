<?php
namespace sbronsted;

class SampleMysql extends Sample {

	public function __construct($data = array()) {
		parent::__construct($data);
		self::$db = 'mysql';
	}
}
