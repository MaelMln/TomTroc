<?php

namespace App\Repository;

use App\Service\Database;

class HomeRepository extends AbstractRepository
{
	protected $db;

	public function getData()
	{
		$stmt = $this->db->prepare("SELECT 'Hello World depuis la base de données!' AS message");
		$stmt->execute();
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
}
