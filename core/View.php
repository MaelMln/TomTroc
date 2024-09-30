<?php

namespace Core;

class View {
	public function render(string $viewPath, array $data = []) {
		extract($data);

		$fullPath = __DIR__ . '/../app/Views/' . $viewPath . '.php';

		if (file_exists($fullPath)) {
			include $fullPath;
		} else {
			throw new \Exception("Vue {$viewPath} non trouvée.");
		}
	}
}
