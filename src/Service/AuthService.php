<?php

namespace App\Service;

use App\Exception\UnauthorizedException;
use App\Exception\MethodNotAllowedException;

class AuthService
{
	public static function ensureUserLoggedIn(): void
	{
		if (!isset($_SESSION['user'])) {
			throw new UnauthorizedException("Vous devez être connecté pour effectuer cette action.");
		}
	}

	public static function ensureMethodIs(string $method): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
			throw new MethodNotAllowedException("Méthode non autorisée. Attendu : $method");
		}
	}
}
