<?php

namespace App\Util;

class TextReformat
{
	public static function camelToSnake(string $input): string
	{
		$pattern = '/[A-Z]/';
		$replacement = '_$0';
		$snake = strtolower(preg_replace($pattern, $replacement, $input));
		return ltrim($snake, '_');
	}

	public static function snakeToCamel(string $input): string
	{
		$str = str_replace('_', '', ucwords($input, '_'));
		return lcfirst($str);
	}
}
