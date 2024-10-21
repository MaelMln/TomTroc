<?php

namespace App\Entity;

use App\Service\Database;

abstract class AbstractEntity
{
	protected $db;

	public function __construct()
	{
		$this->db = Database::getInstance();
	}
}

