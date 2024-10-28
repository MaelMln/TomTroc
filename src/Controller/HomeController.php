<?php

namespace App\Controller;

class HomeController extends AbstractController
{
	public function index()
	{
		$this->view('home/index');
	}
}
