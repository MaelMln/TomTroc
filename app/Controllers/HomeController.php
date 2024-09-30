<?php

namespace App\Controllers;

use Core\Controller;

class HomeController extends Controller {
	public function index() {
		$model = $this->model('HomeModel');
		$data = $model->getData();
		$this->view('home/index', $data);
	}
}
