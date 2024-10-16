<?php

spl_autoload_register(function ($class) {
	$prefixes = [
		'App\\Controller\\' => __DIR__ . '/../src/Controller/',
		'App\\Entity\\' => __DIR__ . '/../src/Entity/',
		'Core\\' => __DIR__ . 'autoload.php/',
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
