<?php

namespace App\Controller;

class HomeController extends AbstractController
{
	public function index()
	{
		$data = [
			'title' => 'Accueil - TomTroc',
			'additionalCss' => ['home.css'],
		];
		$this->view('home/index', $data);
	}
}