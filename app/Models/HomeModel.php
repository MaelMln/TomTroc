<?php

namespace App\Models;

use Core\Model;

class HomeModel extends Model {
	public function getData() {
		$stmt = $this->db->prepare("SELECT 'Hello World depuis la base de données!' AS message");
		$stmt->execute();
		return $stmt->fetch(\PDO::FETCH_ASSOC);
	}
}
