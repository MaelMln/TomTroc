<?php

namespace App\Controller;

use App\Exception\MethodNotAllowedException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Service\RateLimit;
use App\Service\AuthService;
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
		AuthService::ensureUserLoggedIn();

		$userId = $_SESSION['user']['id'];
		$activeConversationId = isset($_GET['conversation_id']) ? (int)$_GET['conversation_id'] : null;

		$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		$limit = 10;
		$offset = ($page - 1) * $limit;

		$conversations = $this->conversationRepo->findAllConversationsWithUsersByUserId($userId, $limit, $offset);
		$totalConversations = $this->conversationRepo->countAllConversationsByUserId($userId);
		$totalPages = ceil($totalConversations / $limit);

		$activeConversation = null;
		$messages = [];
		if ($activeConversationId) {
			$temp = $this->conversationRepo->findById($activeConversationId);

			if ($temp
				&& ($temp->getUserOneId() === $userId || $temp->getUserTwoId() === $userId)) {
				$activeConversation = $temp;
				$messages = $this->messageRepo->findMessagesByConversation($activeConversation->getId());
				$this->messageRepo->markAllAsRead($activeConversation->getId(), $userId);
			}
		}

		$data = [
			'title' => 'Messagerie - TomTroc',
			'additionalCss' => ['messaging.css'],
			'additionnalJs' => ['messagingMain.js'],
			'conversations' => $conversations,
			'userId' => $userId,
			'currentPage' => $page,
			'totalPages' => $totalPages,
			'activeConversation' => $activeConversation,
			'messages' => $messages,
		];

		$this->view('messaging/main', $data);
	}



	public function viewConversation($conversation_id)
	{
		AuthService::ensureUserLoggedIn();

		$conversationId = filter_var($conversation_id, FILTER_VALIDATE_INT);
		if (!$conversationId) {
			throw new NotFoundException("Conversation non trouvée.");
		}

		$conversation = $this->conversationRepo->findById($conversationId);
		if (!$conversation
			|| ($conversation->getUserOneId() !== $_SESSION['user']['id']
				&& $conversation->getUserTwoId() !== $_SESSION['user']['id'])) {
			throw new UnauthorizedException("Accès interdit à cette conversation.");
		}

		$messages = $this->messageRepo->findMessagesByConversation($conversationId);
		$this->messageRepo->markAllAsRead($conversationId, $_SESSION['user']['id']);

		$data = [
			'title' => 'Conversation',
			'additionalCss' => ['messaging.css'],
			'additionnalJs' => ['messagingView.js'],
			'conversation' => $conversation,
			'messages' => $messages,
			'errors' => [],
		];
		$this->view('messaging/view', $data);
	}

	public function sendAjax()
	{
		try {
			AuthService::ensureMethodIs('POST');
			AuthService::ensureUserLoggedIn();

			$input = json_decode(file_get_contents('php://input'), true) ?? [];
			$conversationId = isset($input['conversation_id']) ? (int)$input['conversation_id'] : null;
			$messageContent = isset($input['message']) ? trim($input['message']) : '';

			if (!$conversationId || $messageContent === '') {
				throw new Exception("Paramètres manquants ou message vide.");
			}

			$conversation = $this->conversationRepo->findById($conversationId);
			if (!$conversation
				|| ($conversation->getUserOneId() !== $_SESSION['user']['id']
					&& $conversation->getUserTwoId() !== $_SESSION['user']['id'])) {
				throw new UnauthorizedException("Accès interdit à cette conversation.");
			}

			if (!$this->rateLimit->isAllowed('send_message_' . $_SESSION['user']['id'])) {
				throw new Exception("Trop de messages envoyés. Veuillez réessayer plus tard.");
			}

			$message = $this->messageRepo->createMessage(
				conversationId: $conversationId,
				senderId: $_SESSION['user']['id'],
				content: $messageContent
			);

			$this->jsonResponse(['success' => true, 'message_id' => $message->getId()], 200);
		} catch (MethodNotAllowedException $e) {
			$this->jsonResponse(['error' => $e->getMessage()], 405);
		} catch (UnauthorizedException $e) {
			$this->jsonResponse(['error' => $e->getMessage()], 403);
		} catch (Exception $e) {
			$this->jsonResponse(['error' => $e->getMessage()], 400);
		}
	}

	public function fetchMessages()
	{
		AuthService::ensureUserLoggedIn();

		$conversationId = isset($_GET['conversation_id']) ? (int)$_GET['conversation_id'] : null;
		$lastMessageId = isset($_GET['last_message_id']) ? (int)$_GET['last_message_id'] : 0;

		if (!$conversationId) {
			$this->jsonResponse(['error' => 'Paramètres manquants'], 400);
			return;
		}

		$conversation = $this->conversationRepo->findById($conversationId);
		if (!$conversation
			|| ($conversation->getUserOneId() !== $_SESSION['user']['id']
				&& $conversation->getUserTwoId() !== $_SESSION['user']['id'])) {
			$this->jsonResponse(['error' => 'Accès interdit'], 403);
			return;
		}

		$newMessages = $this->messageRepo->findNewMessages($conversationId, $lastMessageId);

		$this->messageRepo->markAllAsRead($conversationId, $_SESSION['user']['id']);

		$messagesData = [];
		foreach ($newMessages as $message) {
			$messagesData[] = [
				'id' => $message->getId(),
				'conversation_id' => $message->getConversationId(),
				'sender_id' => $message->getSenderId(),
				'content' => htmlspecialchars($message->getContent()),
				'sent_at' => $message->getSentAt(),
				'is_read_by_user_one' => $message->isReadByUserOne(),
				'is_read_by_user_two' => $message->isReadByUserTwo(),
			];
		}


		$this->jsonResponse(['messages' => $messagesData]);
	}

	public function fetchConversation()
	{
		AuthService::ensureMethodIs('GET');
		AuthService::ensureUserLoggedIn();

		$conversationId = isset($_GET['conversation_id']) ? (int)$_GET['conversation_id'] : null;
		if (!$conversationId) {
			$this->jsonResponse(['error' => 'Paramètres manquants'], 400);
			return;
		}

		$conversation = $this->conversationRepo->findById($conversationId);
		if (!$conversation
			|| ($conversation->getUserOneId() !== $_SESSION['user']['id']
				&& $conversation->getUserTwoId() !== $_SESSION['user']['id'])) {
			$this->jsonResponse(['error' => 'Accès interdit'], 403);
			return;
		}

		$this->messageRepo->markAllAsRead($conversationId, $_SESSION['user']['id']);
		$messages = $this->messageRepo->findMessagesByConversation($conversationId);

		$messagesData = [];
		foreach ($messages as $message) {
			$messagesData[] = [
				'id' => $message->getId(),
				'conversation_id' => $message->getConversationId(),
				'sender_id' => $message->getSenderId(),
				'content' => htmlspecialchars($message->getContent()),
				'sent_at' => $message->getSentAt(),
				'is_read_by_user_one' => $message->isReadByUserOne(),
				'is_read_by_user_two' => $message->isReadByUserTwo(),
			];
		}

		$this->jsonResponse(['messages' => $messagesData]);
	}

	public function countUnread()
	{
		try {
			AuthService::ensureMethodIs('GET');
			AuthService::ensureUserLoggedIn();

			$userId = $_SESSION['user']['id'];
			$count = $this->messageRepo->countNewMessages($userId);

			$this->jsonResponse(['count' => $count], 200);
		} catch (MethodNotAllowedException $e) {
			$this->jsonResponse(['error' => $e->getMessage()], 405);
		} catch (UnauthorizedException $e) {
			$this->jsonResponse(['error' => $e->getMessage()], 403);
		} catch (Exception $e) {
			$this->jsonResponse(['error' => 'Erreur interne du serveur.'], 500);
		}
	}

	private function jsonResponse(array $data, int $statusCode = 200): void
	{
		http_response_code($statusCode);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($data);
		exit;
	}
}
