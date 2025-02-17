<?php

namespace App;

use App\Exception\MethodNotAllowedException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Util\Config;
use App\Util\Router;

class Kernel
{
	private $config;

	public function __construct() {
		$configInstance = Config::getInstance();
		$this->config = $configInstance->all();

		if ($this->config['env'] === 'development') {
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		} else {
			ini_set('display_errors', 0);
			ini_set('display_startup_errors', 0);
			error_reporting(E_ALL);
		}

		session_start();

	}
	public function handleRequest()
	{
		$routes = require ROOT_DIR . '/config/routes/routes.php';
		$requestUri = $_SERVER['REQUEST_URI'] ?? "/";

		try {
			$router = new Router($routes);
			$router->dispatch($requestUri);
		} catch (\Throwable $e) {
			$this->handleException($e);
		}
	}

	public function handleException($exception)
	{
		$baseUrl = $this->config['base_url'];

		if ($exception instanceof NotFoundException) {
			$statusCode = 404;
			$statusMessage = "Page non trouvée.";
		} elseif ($exception instanceof UnauthorizedException) {
			$statusCode = 403;
			$statusMessage = "Accès interdit.";
		} elseif ($exception instanceof MethodNotAllowedException) {
			$statusCode = 405;
			$statusMessage = "Méthode non autorisée.";
		} else {
			$statusCode = 500;
			$statusMessage = "Erreur interne du serveur.";
		}

		http_response_code($statusCode);

		$data = [
			'statusCode' => $statusCode,
			'statusMessage' => $statusMessage,
			'exception' => $exception,
			'baseUrl' => $baseUrl,
			'config' => $this->config,
		];

		extract($data);
		include ROOT_DIR . '/src/View/errors/error.php';

		error_log($exception);
	}
}
