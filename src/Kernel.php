<?php

namespace App;

use App\Util\Router;

class Kernel
{
	public function handleRequest()
	{
		$routes = include __DIR__ . '/../config/routes/routes.php';
		$requestUri = isset($_GET['url']) ? '/' . filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL) : '/';

		$router = new Router($routes);
		$router->dispatch($requestUri);
	}
}
