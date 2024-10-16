<?php

namespace App\Controller;

use App\Service\ViewRenderer;

abstract class AbstractController
{
	public function getEntity(string $entity)
	{
		$entity = "App\\Entity\\" . $entity;
		if (class_exists($entity)) {
			return new $entity();
		} else {
			throw new \Exception("Entité {$entity} non trouvée.");
		}
	}

	public function view(string $viewPath, array $data = [])
	{
		$view = new ViewRenderer();
		$view->render($viewPath, $data);
	}
}

