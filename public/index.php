<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


define('ROOT_DIR', dirname(__DIR__));

require_once __DIR__ . '/../src/Util/Autoloader.php';
\App\Util\Autoloader::register();

use App\Kernel;

$kernel = new Kernel();
$kernel->handleRequest();
