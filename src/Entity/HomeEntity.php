<?php

namespace App\Entity;

class HomeEntity extends AbstractEntity
{
	private $message;

	public function __construct(string $message)
	{
		parent::__construct();
		$this->message = $message;
	}

	public function getMessage(): string
	{
		return $this->message;
	}
}
