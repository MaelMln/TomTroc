<?php

namespace App;

use App\Util\Router;

class Kernel
{
	public function handleRequest()
	{
		$routes = include __DIR__ . '/../config/routes/routes.php';
		$requestUri = $_SERVER['REQUEST_URI'] ?? "/";

		$router = new Router($routes);
		$router->dispatch($requestUri);
	}
}
