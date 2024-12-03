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
	'login' => [
		'path' => '/login',
		'controller' => 'App\Controller\UserController::login',
	],
	'logout' => [
		'path' => '/logout',
		'controller' => 'App\Controller\UserController::logout',
	],
	'list_users' => [
		'path' => '/users',
		'controller' => 'App\Controller\UserController::list',
	],
];