<?php

namespace App\Exception;

use Exception;

class UnauthorizedException extends Exception
{
	protected $message = 'Accès non autorisé.';
	protected $code = 403;
}
