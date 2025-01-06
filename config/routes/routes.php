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
	'list_books' => [
        'path' => '/books',
        'controller' => 'App\Controller\BookController::index',
    ],
    'create_book' => [
        'path' => '/books/create',
        'controller' => 'App\Controller\BookController::create',
    ],
    'edit_book' => [
        'path' => '/books/edit',
        'controller' => 'App\Controller\BookController::edit',
    ],
    'delete_book' => [
        'path' => '/books/delete',
        'controller' => 'App\Controller\BookController::delete',
    ],
    'show_book' => [
        'path' => '/books/show',
        'controller' => 'App\Controller\BookController::show',
    ],
	'profile' => [
		'path' => '/profile',
		'controller' => 'App\Controller\UserController::profile',
	],
];