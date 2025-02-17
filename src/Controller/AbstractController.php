<?php

namespace App\Controller;

use App\Repository\MessageRepository;
use App\Util\Config;
use Exception;
use App\Service\ViewRenderer;

abstract class AbstractController
{
	protected string $baseUrl;

	public function __construct()
	{
		$config = Config::getInstance();
		$this->baseUrl = $config->get('base_url');
	}

	public function getEntity(string $entity)
	{
		$entityClass = "App\\Entity\\" . $entity;
		if (!class_exists($entityClass)) {
			throw new Exception("Entité {$entityClass} non trouvée.");
		}

		return new $entityClass();
	}

	protected function share(array &$data)
	{
		if (isset($_SESSION['user'])) {
			$messageRepo = new MessageRepository();
			$newMessagesCount = $messageRepo->countNewMessages($_SESSION['user']['id']);
			$data['newMessagesCount'] = $newMessagesCount;
		}
	}

	public function view(string $viewPath, array $data = [])
	{
		$this->share($data);
		$view = new ViewRenderer();
		$view->render($viewPath, $data);
	}
}
