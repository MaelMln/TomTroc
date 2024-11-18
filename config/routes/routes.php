<?php
return [
	'home' => [
		'path' => '/',
		'controller' => 'App\Controller\HomeController::index',
	],
	'register' => [
		'path' => '/register',
		'controller' => 'App\Controller\UserController::register',
	],
	'list_users' => [
		'path' => '/users',
		'controller' => 'App\Controller\UserController::list',
	],
];