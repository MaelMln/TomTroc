<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

require_once __DIR__ . '/../core/autoload.php';

$routes = include __DIR__ . '/../config/routes/routes.php';

$requestUri = isset($_GET['url']) ? '/' . filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL) : '/';

$routeFound = false;
foreach ($routes as $route) {
	if ($route['path'] === $requestUri) {
		$routeFound = true;
		$controllerMethod = explode('::', $route['controller']);
		$controllerClass = $controllerMethod[0];
		$method = $controllerMethod[1];

		if (class_exists($controllerClass)) {
			$controller = new $controllerClass();

			if (method_exists($controller, $method)) {
				$controller->$method();
			} else {
				echo "Erreur 404 : Méthode {$method} non trouvée dans le contrôleur {$controllerClass}.";
			}
		} else {
			echo "Erreur 404 : Contrôleur {$controllerClass} non trouvé.";
		}
		break;
	}
}

if (!$routeFound) {
	echo "Erreur 404 : Page non trouvée.";
}
