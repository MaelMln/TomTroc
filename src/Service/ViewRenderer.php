<?php

namespace App\Service;

class ViewRenderer
{
	public function render(string $viewPath, array $data = [])
	{
		extract($data);

		$fullViewPath = __DIR__ . '/../View/' . $viewPath . '.php';
		$basePath = __DIR__ . '/../View/layout/base.php';

		if (file_exists($fullViewPath) && file_exists($basePath)) {
			$content = $fullViewPath;
			include $basePath;
		} else {
			throw new \Exception("Vue ou layout non trouvé.");
		}
	}
}