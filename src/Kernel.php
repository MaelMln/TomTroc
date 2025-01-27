<?php

namespace App;

use App\Exception\MethodNotAllowedException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Util\Router;

class Kernel
{
	public function handleRequest()
	{
		$config = require ROOT_DIR . '/config/config.php';
		$routes = include ROOT_DIR . '/config/routes/routes.php';
		$requestUri = $_SERVER['REQUEST_URI'] ?? "/";

		$router = new Router($routes);
		$router->dispatch($requestUri);
	}

	public function handleException($exception)
	{
		$config = require ROOT_DIR . '/config/config.php';
		$baseUrl = $config['base_url'];

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
			'config' => $config,
		];

		extract($data);
		include ROOT_DIR . '/src/View/errors/error.php';

		error_log($exception);
	}
}
