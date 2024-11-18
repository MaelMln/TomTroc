<?php

namespace App\Controller;

use Exception;
use App\Service\ViewRenderer;

abstract class AbstractController
{
	protected string $baseUrl;

	public function __construct()
	{
		$config = require ROOT_DIR . '/config/config.php';
		$this->baseUrl = $config['base_url'];
	}

	public function getEntity(string $entity)
	{
		$entityClass = "App\\Entity\\" . $entity;
		if (!class_exists($entityClass)) {
			throw new Exception("Entité {$entityClass} non trouvée.");
		}

		return new $entityClass();
	}

	public function view(string $viewPath, array $data = [])
	{
		$view = new ViewRenderer();
		$view->render($viewPath, $data);
	}
}
