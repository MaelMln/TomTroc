<?php

namespace App\Entity;

use App\Controllers\AbstractEntity;

class HomeEntity extends AbstractEntity {
	public function getData() {
		$stmt = $this->db->prepare("SELECT 'Hello World depuis la base de données!' AS message");
		$stmt->execute();
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
}
