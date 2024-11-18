<?php

namespace App\Util\TextReformat;

function camelToSnake($input)
{
	$pattern = '/[A-Z]/';
	$replacement = '_$0';
	$snake = strtolower(preg_replace($pattern, $replacement, $input));
	return ltrim($snake, '_');
}

?>
