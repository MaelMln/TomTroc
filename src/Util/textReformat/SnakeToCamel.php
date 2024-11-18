<?php

namespace App\Util\TextReformat;

function snakeToCamel($input)
{
	$str = str_replace('_', '', ucwords($input, '_'));
	$str = lcfirst($str);
	return $str;
}

?>