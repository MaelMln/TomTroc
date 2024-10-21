<?php


namespace App\Controller;

use App\Entity\HomeEntity;
use App\Repository\HomeRepository;

class HomeController extends AbstractController
{
	public function index()
	{
		$repository = new HomeRepository();
		$data = $repository->getData();
		$entity = new HomeEntity($data['message']);

		$this->view('home/index', ['message' => $entity->getMessage()]);
	}
}

