<?php

$config = require __DIR__ . '/../config/config.php';

if ($config['env'] === 'development') {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
} else {
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(E_ALL);
}

session_start();

define('ROOT_DIR', dirname(__DIR__));

require_once __DIR__ . '/../src/Util/Autoloader.php';

use App\Util\Autoloader;
use App\Kernel;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Exception\MethodNotAllowedException;

Autoloader::register();

set_exception_handler(function ($exception) {
	$config = require ROOT_DIR . '/config/config.php';
	$baseUrl = $config['base_url'];

	if ($exception instanceof NotFoundException) {
		http_response_code(404);
		include ROOT_DIR . '/src/View/errors/404.php';
	} elseif ($exception instanceof UnauthorizedException) {
		http_response_code(403);
		include ROOT_DIR . '/src/View/errors/403.php';
	} elseif ($exception instanceof MethodNotAllowedException) {
		http_response_code(405);
		include ROOT_DIR . '/src/View/errors/405.php';
	} else {
		http_response_code(500);
		include ROOT_DIR . '/src/View/errors/500.php';
	}

	error_log($exception);
});


$kernel = new Kernel();
try {
	$kernel->handleRequest();
} catch (Exception $e) {
	throw $e;
}
