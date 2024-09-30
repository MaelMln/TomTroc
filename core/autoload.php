<?php

spl_autoload_register(function ($class) {
	$prefixes = [
		'App\\Controllers\\' => __DIR__ . '/../app/Controllers/',
		'App\\Models\\'      => __DIR__ . '/../app/Models/',
		'Core\\'             => __DIR__ . '/',
	];

	foreach ($prefixes as $prefix => $base_dir) {
		$len = strlen($prefix);
		if (strncmp($prefix, $class, $len) !== 0) {
			continue;
		}

		$relative_class = substr($class, $len);

		$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

		if (file_exists($file)) {
			require $file;
			return;
		}
	}
});
