<?php

namespace App\Service;

use Exception;

class ImageUploadService
{
	private string $rootDir;

	public function __construct(string $rootDir)
	{
		$this->rootDir = rtrim($rootDir, '/');
	}

	public function upload(array $file, string $subDir = ''): string
	{
		if (!isset($file['tmp_name'], $file['name'], $file['error'])) {
			throw new Exception("Fichier d'upload invalide.");
		}

		if ($file['error'] !== UPLOAD_ERR_OK) {
			throw new Exception("Erreur lors de l'upload (code " . $file['error'] . ")");
		}

		$uploadPath = $this->rootDir . '/public/assets/uploads/';
		if ($subDir) {
			$uploadPath .= rtrim($subDir, '/') . '/';
		}

		if (!is_dir($uploadPath)) {
			mkdir($uploadPath, 0755, true);
		}

		$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
		if (!in_array($extension, $allowedExtensions)) {
			throw new Exception("Type d'image non supporté. Extensions permises : jpg, jpeg, png, gif.");
		}

		$uniqueName = uniqid() . '.' . $extension;
		$destination = $uploadPath . $uniqueName;

		if (!move_uploaded_file($file['tmp_name'], $destination)) {
			throw new Exception("Impossible de déplacer le fichier uploadé.");
		}

		$relativePath = '/assets/uploads/';
		if ($subDir) {
			$relativePath .= rtrim($subDir, '/') . '/';
		}
		$relativePath .= $uniqueName;

		return $relativePath;
	}

	public function delete(string $relativePath): void
	{
		$fullPath = $this->rootDir . '/public' . $relativePath;

		if (file_exists($fullPath)) {
			unlink($fullPath);
		}
	}
}
