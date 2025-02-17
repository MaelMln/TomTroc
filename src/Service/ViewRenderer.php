<?php

namespace App\Service;

use App\Util\Config;

class ViewRenderer
{
	public function render(string $viewPath, array $data = [])
	{
		$config = Config::getInstance();
		$data['baseUrl'] = $config->get('base_url');

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
