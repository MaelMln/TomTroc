<?php

namespace App\Util;

use Exception;

class Router
{
	private $routes;

	public function __construct(array $routes)
	{
		$this->routes = $routes;
	}

	public function dispatch(string $requestUri)
	{
		$routeFound = false;

		foreach ($this->routes as $route) {
			if ($route['path'] === $requestUri) {
				$routeFound = true;
				$controllerMethod = explode('::', $route['controller']);
				$controllerClass = $controllerMethod[0];
				$method = $controllerMethod[1];

				if (!class_exists($controllerClass)) {
					throw new Exception("Erreur 404 : Contrôleur {$controllerClass} non trouvé.");
				}

				$controller = new $controllerClass();

				if (!method_exists($controller, $method)) {
					throw new Exception("Erreur 404 : Méthode {$method} non trouvée dans le contrôleur {$controllerClass}.");
				}

				$controller->$method();
				break;
			}
		}

		if (!$routeFound) {
			throw new Exception("Erreur 404 : Page non trouvée.");
		}
	}
}
