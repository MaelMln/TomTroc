<?php

namespace App\Controller;

use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Service\RateLimit;
use Exception;

class MessagingController extends AbstractController
{
	private ConversationRepository $conversationRepo;
	private MessageRepository $messageRepo;
	private RateLimit $rateLimit;


	public function __construct()
	{
		parent::__construct();
		$this->conversationRepo = new ConversationRepository();
		$this->messageRepo = new MessageRepository();
		$this->rateLimit = new RateLimit();
	}

	public function main()
	{
		if (!isset($_SESSION['user'])) {
			header('Location: ' . $this->baseUrl . '/login');
			exit;
		}

		$userId = $_SESSION['user']['id'];
		$page = isset($_GET['page']) && filter_var($_GET['page'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) ? (int)$_GET['page'] : 1;
		$limit = 10;
		$offset = ($page - 1) * $limit;

		$conversations = $this->conversationRepo->findAllConversationsWithUsersByUserId($userId, $limit, $offset);
		$totalConversations = $this->conversationRepo->countAllConversationsByUserId($userId);
		$totalPages = ceil($totalConversations / $limit);

		$data = [
			'title' => 'Messagerie - TomTroc',
			'additionalCss' => ['messaging.css'],
			'conversations' => $conversations,
			'userId' => $userId,
			'currentPage' => $page,
			'totalPages' => $totalPages,
		];
		$this->view('messaging/main', $data);
	}


	public function send()
	{
		if (!isset($_SESSION['user'])) {
			header('Location: ' . $this->baseUrl . '/login');
			exit;
		}

		$toUserId = isset($_GET['to']) ? (int)$_GET['to'] : null;
		$bookId = isset($_GET['book']) ? (int)$_GET['book'] : null;

		if (!$toUserId || !$bookId) {
			header('Location: ' . $this->baseUrl . '/books');
			exit;
		}

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$content = trim($_POST['message'] ?? '');
			$errors = [];

			if ($content === '') {
				$errors[] = 'Le message ne peut pas être vide.';
			} else {
				$currentUserId = $_SESSION['user']['id'];
				$recipientUserId = $toUserId;

				$conversation = $this->conversationRepo->findConversation($currentUserId, $recipientUserId);
				if (!$conversation) {
					$conversation = $this->conversationRepo->createConversation($currentUserId, $recipientUserId);
				}

				if ($conversation->getId() === null) {
					$errors[] = 'Erreur lors de la création de la conversation.';
				} else {
					$message = $this->messageRepo->createMessage(
						conversationId: $conversation->getId(),
						senderId: $currentUserId,
						content: $content
					);

					header('Location: ' . $this->baseUrl . '/messages/view/' . $conversation->getId());
					exit;
				}
			}

			if (!empty($errors)) {
				$data = [
					'title' => 'Envoyer un message',
					'additionalCss' => ['messaging.css'],
					'toUserId' => $toUserId,
					'bookId' => $bookId,
					'errors' => $errors,
				];
				$this->view('messaging/send', $data);
				return;
			}
		}

		$data = [
			'title' => 'Envoyer un message',
			'additionalCss' => ['messaging.css'],
			'toUserId' => $toUserId,
			'bookId' => $bookId,
			'errors' => [],
		];
		$this->view('messaging/send', $data);
	}


	public function viewConversation($conversation_id)
	{
		if (!isset($_SESSION['user'])) {
			header('Location: ' . $this->baseUrl . '/login');
			exit;
		}

		$conversationId = (int)$conversation_id;
		if (!$conversationId) {
			header('Location: ' . $this->baseUrl . '/books');
			exit;
		}

		$conversation = $this->conversationRepo->findById($conversationId);
		if (!$conversation ||
			($conversation->getUserOneId() !== $_SESSION['user']['id'] &&
				$conversation->getUserTwoId() !== $_SESSION['user']['id'])) {
			throw new \App\Exception\UnauthorizedException("Accès interdit à cette conversation.");
		}

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$content = trim($_POST['message'] ?? '');
			$errors = [];

			if ($content === '') {
				$errors[] = 'Le message ne peut pas être vide.';
			} else {
				$message = $this->messageRepo->createMessage(
					conversationId: $conversation->getId(),
					senderId: $_SESSION['user']['id'],
					content: $content
				);
				header('Location: ' . $this->baseUrl . '/messages/view/' . $conversation->getId());
				exit;
			}
		}

		$messages = $this->messageRepo->findMessagesByConversation($conversationId);

		$data = [
			'title' => 'Conversation',
			'additionalCss' => ['messaging.css'],
			'conversation' => $conversation,
			'messages' => $messages,
			'errors' => $errors ?? [],
		];
		$this->view('messaging/view', $data);
	}


	public function fetchMessages()
	{
		if (!isset($_SESSION['user'])) {
			http_response_code(401);
			echo json_encode(['error' => 'Non autorisé']);
			exit;
		}

		$conversationId = $_GET['conversation_id'] ?? null;
		$lastMessageId = $_GET['last_message_id'] ?? 0;

		if (!$conversationId) {
			http_response_code(400);
			echo json_encode(['error' => 'Paramètres manquants']);
			exit;
		}

		$conversation = $this->conversationRepo->findById((int)$conversationId);
		if (!$conversation ||
			($conversation->getUserOneId() !== $_SESSION['user']['id'] &&
				$conversation->getUserTwoId() !== $_SESSION['user']['id'])) {
			http_response_code(403);
			echo json_encode(['error' => 'Accès interdit']);
			exit;
		}

		$newMessages = $this->messageRepo->findNewMessages((int)$conversationId, (int)$lastMessageId);

		$messagesData = [];
		foreach ($newMessages as $message) {
			$messagesData[] = [
				'id' => $message->getId(),
				'conversation_id' => $message->getConversationId(),
				'sender_id' => $message->getSenderId(),
				'content' => $message->getContent(),
				'sent_at' => $message->getSentAt(),
			];
		}

		echo json_encode(['messages' => $messagesData]);
		exit;
	}

	public function sendAjax()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			http_response_code(405);
			echo json_encode(['error' => 'Méthode non autorisée']);
			exit;
		}

		if (!isset($_SESSION['user'])) {
			http_response_code(401);
			echo json_encode(['error' => 'Non autorisé']);
			exit;
		}

		$input = json_decode(file_get_contents('php://input'), true);
		$conversationId = $input['conversation_id'] ?? null;
		$messageContent = trim($input['message'] ?? '');

		if (!$conversationId || $messageContent === '') {
			http_response_code(400);
			echo json_encode(['error' => 'Paramètres manquants ou message vide']);
			exit;
		}

		$conversation = $this->conversationRepo->findById((int)$conversationId);
		if (!$conversation ||
			($conversation->getUserOneId() !== $_SESSION['user']['id'] &&
				$conversation->getUserTwoId() !== $_SESSION['user']['id'])) {
			http_response_code(403);
			echo json_encode(['error' => 'Accès interdit']);
			exit;
		}

		if (!$this->rateLimit->isAllowed('send_message_' . $_SESSION['user']['id'])) {
			http_response_code(429);
			echo json_encode(['error' => 'Trop de messages envoyés. Veuillez réessayer plus tard.']);
			exit;
		}

		$message = $this->messageRepo->createMessage(
			conversationId: (int)$conversationId,
			senderId: $_SESSION['user']['id'],
			content: $messageContent
		);

		echo json_encode(['success' => true, 'message_id' => $message->getId()]);
		exit;
	}

	public function fetchConversation()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
			http_response_code(405);
			echo json_encode(['error' => 'Méthode non autorisée']);
			exit;
		}

		if (!isset($_SESSION['user'])) {
			http_response_code(401);
			echo json_encode(['error' => 'Non autorisé']);
			exit;
		}

		$conversationId = $_GET['conversation_id'] ?? null;
		if (!$conversationId) {
			http_response_code(400);
			echo json_encode(['error' => 'Paramètres manquants']);
			exit;
		}

		$conversation = $this->conversationRepo->findById((int)$conversationId);
		if (!$conversation ||
			($conversation->getUserOneId() !== $_SESSION['user']['id'] &&
				$conversation->getUserTwoId() !== $_SESSION['user']['id'])) {
			http_response_code(403);
			echo json_encode(['error' => 'Accès interdit']);
			exit;
		}

		$messages = $this->messageRepo->findMessagesByConversation((int)$conversationId);

		$messagesData = [];
		foreach ($messages as $message) {
			$messagesData[] = [
				'id' => $message->getId(),
				'conversation_id' => $message->getConversationId(),
				'sender_id' => $message->getSenderId(),
				'content' => $message->getContent(),
				'sent_at' => $message->getSentAt(),
			];
		}

		echo json_encode(['messages' => $messagesData]);
		exit;
	}

}
