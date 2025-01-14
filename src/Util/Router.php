<?php

namespace App\Util;

use App\Exception\NotFoundException;

class Router
{
	private $routes;

	public function __construct(array $routes)
	{
		$this->routes = $routes;
	}

	public function dispatch(string $requestUri)
	{
		$parsedUrl = parse_url($requestUri);
		$path = $parsedUrl['path'] ?? '/';
		$query = $parsedUrl['query'] ?? '';

		foreach ($this->routes as $routeName => $route) {
			$routePath = $route['path'];
			$pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<\1>[a-zA-Z0-9_-]+)', $routePath);
			$pattern = "#^" . $pattern . "/?$#";

			if (preg_match($pattern, $path, $matches)) {
				$params = [];
				foreach ($matches as $key => $value) {
					if (is_string($key)) {
						$params[$key] = $value;
					}
				}

				$controllerMethod = explode('::', $route['controller']);
				$controllerClass = $controllerMethod[0];
				$method = $controllerMethod[1];

				if (!class_exists($controllerClass)) {
					throw new NotFoundException("Contrôleur {$controllerClass} non trouvé.");
				}

				$controller = new $controllerClass();

				if (!method_exists($controller, $method)) {
					throw new NotFoundException("Méthode {$method} non trouvée dans le contrôleur {$controllerClass}.");
				}

				call_user_func_array([$controller, $method], $params);
				return;
			}
		}

		throw new NotFoundException("Page non trouvée.");
	}

}