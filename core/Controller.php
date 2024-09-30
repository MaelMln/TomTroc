<?php

namespace Core;

class Controller {
	public function model(string $model) {
		$model = "App\\Models\\" . $model;
		if (class_exists($model)) {
			return new $model();
		} else {
			throw new \Exception("Modèle {$model} non trouvé.");
		}
	}

	public function view(string $viewPath, array $data = []) {
		$view = new View();
		$view->render($viewPath, $data);
	}
}
