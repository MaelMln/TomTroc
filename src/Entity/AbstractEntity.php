<?php

namespace App\Controllers;

use App\Repository\Database;

abstract class AbstractEntity {
	protected $db;

	public function __construct() {
		$this->db = Database::getInstance();
	}
}
