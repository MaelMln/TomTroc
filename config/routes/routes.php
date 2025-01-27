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
		'path' => '/books/edit/{id}',
		'controller' => 'App\Controller\BookController::edit',
	],
	'delete_book' => [
		'path' => '/books/delete/{id}',
		'controller' => 'App\Controller\BookController::delete',
	],
	'show_book' => [
		'path' => '/books/show/{id}',
		'controller' => 'App\Controller\BookController::show',
	],
	'profile' => [
		'path' => '/profile',
		'controller' => 'App\Controller\UserController::profile',
	],
	'profile_with_id' => [
		'path' => '/profile/{id}',
		'controller' => 'App\Controller\UserController::profile',
	],
	'main_messaging' => [
		'path' => '/messages',
		'controller' => 'App\Controller\MessagingController::main',
	],
	'view_conversation' => [
		'path' => '/messages/view/{conversation_id}',
		'controller' => 'App\Controller\MessagingController::viewConversation',
	],
	'fetch_messages' => [
		'path' => '/messages/fetch',
		'controller' => 'App\Controller\MessagingController::fetchMessages',
	],
	'send_ajax' => [
		'path' => '/messages/send_ajax',
		'controller' => 'App\Controller\MessagingController::sendAjax',
	],
	'fetch_conversation' => [
		'path' => '/messages/fetch_conversation',
		'controller' => 'App\Controller\MessagingController::fetchConversation',
	],
	'fetch_unread_count' => [
		'path' => '/messages/count_unread',
		'controller' => 'App\Controller\MessagingController::countUnread',
	],
	'start_conversation' => [
		'path' => '/conversation/start/{to_user_id}',
		'controller' => 'App\Controller\ConversationController::startConversation',
	],
];
