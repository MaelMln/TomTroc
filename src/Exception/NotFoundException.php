<?php

namespace App\Exception;

use Exception;

class NotFoundException extends Exception
{
	protected $message = 'Page non trouvée.';
	protected $code = 404;
}
