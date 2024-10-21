<?php

namespace App\Util;

class Autoloader
{
	public static function register()
	{
		spl_autoload_register([self::class, 'load']);
	}

	public static function load($class)
	{
		$classpath = ROOT_DIR . '/' . str_replace('\\', '/', $class) . '.php';
		$classpath = str_replace('App', 'src', $classpath);

		if (file_exists($classpath)) {
			require_once $classpath;
		}
	}
}
