<?php

namespace App\Controller;

use Exception;
use App\Service\ViewRenderer;

abstract class AbstractController
{
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


