<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

require_once __DIR__ . '/../core/autoload.php';

$url = isset($_GET['url']) ? explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL)) : [];

$controllerName = !empty($url[0]) ? ucfirst($url[0]) . 'Controller' : 'HomeController';
$method = isset($url[1]) ? $url[1] : 'index';
$params = array_slice($url, 2);

$controllerClass = "App\\Controllers\\{$controllerName}";

if (class_exists($controllerClass)) {
	$controller = new $controllerClass();
	if (method_exists($controller, $method)) {
		call_user_func_array([$controller, $method], $params);
	} else {
		echo "Méthode {$method} non trouvée dans le contrôleur {$controllerName}.";
	}
} else {
	echo "Contrôleur {$controllerName} non trouvé.";
}
