<?php

namespace App\Repository;

use App\Service\Database;

abstract class AbstractRepository
{
	protected $db;

	public function __construct()
	{
		$this->db = Database::getInstance();
	}
}

