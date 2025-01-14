<?php

namespace App\Exception;

use Exception;

class MethodNotAllowedException extends Exception
{
	protected $message = 'Méthode non autorisée.';
	protected $code = 405;
}
