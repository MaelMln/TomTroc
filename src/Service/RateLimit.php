<?php

namespace App\Service;

class RateLimit
{
	private int $limit;
	private int $timeWindow;

	public function __construct(int $limit = 100, int $timeWindow = 3600)
	{
		$this->limit = $limit;
		$this->timeWindow = $timeWindow;
	}

	public function isAllowed(string $action): bool
	{
		if (!isset($_SESSION['rate_limit'])) {
			$_SESSION['rate_limit'] = [];
		}

		$currentTime = time();

		if (!isset($_SESSION['rate_limit'][$action])) {
			$_SESSION['rate_limit'][$action] = [];
		}

		$_SESSION['rate_limit'][$action] = array_filter(
			$_SESSION['rate_limit'][$action],
			function ($timestamp) use ($currentTime) {
				return ($timestamp + $this->timeWindow) > $currentTime;
			}
		);

		if (count($_SESSION['rate_limit'][$action]) >= $this->limit) {
			return false;
		}

		$_SESSION['rate_limit'][$action][] = $currentTime;
		return true;
	}
}
