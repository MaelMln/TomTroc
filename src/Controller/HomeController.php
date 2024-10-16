<?php

namespace App\Controller;

class HomeController extends AbstractController {
	public function index() {
		$model = $this->model('HomeModel');
		$data = $model->getData();
		$this->view('home/index', $data);
	}
}
