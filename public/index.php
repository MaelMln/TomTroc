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

Autoloader::register();

$kernel = new Kernel();
$kernel->handleRequest();
