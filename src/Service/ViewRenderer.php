<?php

namespace App\Service;

class ViewRenderer
{
	public function render(string $viewPath, array $data = [])
	{
		$config = require ROOT_DIR . '/config/config.php';
		$data['baseUrl'] = $config['base_url'];

		extract($data);

		$fullViewPath = __DIR__ . '/../View/' . $viewPath . '.php';
		$basePath = __DIR__ . '/../View/layout/base.php';

		if (file_exists($fullViewPath) && file_exists($basePath)) {
			ob_start();
			include $fullViewPath;
			$content = ob_get_clean();

			include $basePath;
		} else {
			throw new \Exception("Vue ou layout non trouvé.");
		}
	}

}
