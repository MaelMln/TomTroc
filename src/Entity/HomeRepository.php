<?php

namespace App\Entity;

use App\Repository\AbstractRepository;

class HomeRepository
{
	private $message;

	public function __construct(string $message)
	{
		$this->message = $message;
	}

	public function getMessage(): string
	{
		return $this->message;
	}
}
