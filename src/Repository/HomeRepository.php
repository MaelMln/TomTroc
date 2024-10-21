<?php

namespace App\Repository;

use App\Service\Database;

class HomeRepository
{
	protected $db;

	public function __construct()
	{
		$this->db = Database::getInstance();
	}

	public function getData()
	{
		$stmt = $this->db->prepare("SELECT 'Hello World depuis la base de données!' AS message");
		$stmt->execute();
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
}
