<?php

namespace App\Service;

use PDO;
use PDOException;

class Database
{
	private static $instance = null;
	private $connection;

	private function __construct()
	{
		$config = require ROOT_DIR . '/config/config.php';
		$dbConfig = $config['db'];

		try {
			$dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
			$this->connection = new PDO($dsn, $dbConfig['user'], $dbConfig['password']);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			throw new \Exception("Erreur de connexion : " . $e->getMessage());
		}
	}

	public static function getInstance(): PDO
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance->connection;
	}
}
