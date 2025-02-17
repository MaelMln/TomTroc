<?php

namespace App\Util;

class Config
{
	private static ?Config $instance = null;
	private array $config;

	private function __construct()
	{
		$this->config = require ROOT_DIR . '/config/config.php';
	}

	public static function getInstance(): Config
	{
		if (self::$instance === null) {
			self::$instance = new Config();
		}
		return self::$instance;
	}

	public function all(): array
	{
		return $this->config;
	}

	public function get(string $key)
	{
		return $this->config[$key] ?? null;
	}
}
