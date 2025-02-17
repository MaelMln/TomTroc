<?php

require_once __DIR__ . '/../src/Util/Autoloader.php';

define('ROOT_DIR', dirname(__DIR__));

use App\Util\Autoloader;
use App\Kernel;

Autoloader::register();

$kernel = new Kernel();
$kernel->handleRequest();
