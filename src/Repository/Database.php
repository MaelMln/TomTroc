<?php

namespace App\Repository;

use PDO;
use PDOException;

class Database
{
	private static $instance = null;
	private $connection;

	private function __construct()
	{
		$config = require __DIR__ . '/../config/config.php';
		$dbConfig = $config['db'];

		try {
			$dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
			$this->connection = new PDO($dsn, $dbConfig['user'], $dbConfig['password']);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			die("Erreur de connexion : " . $e->getMessage());
		}
	}

	private function __clone()
	{
	}

	private function __wakeup()
	{
	}

	public static function getInstance(): PDO
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance->connection;
	}
}
