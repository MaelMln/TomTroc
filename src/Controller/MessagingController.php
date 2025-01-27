<?php

namespace App\Controller;

use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Service\RateLimit;
use App\Exception\UnauthorizedException;
use App\Exception\NotFoundException;
use App\Exception\MethodNotAllowedException;
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

	public function viewConversation($conversation_id)
	{
		if (!isset($_SESSION['user'])) {
			header('Location: ' . $this->baseUrl . '/login');
			exit;
		}

		$conversationId = filter_var($conversation_id, FILTER_VALIDATE_INT);
		if (!$conversationId) {
			throw new NotFoundException("Conversation non trouvée.");
		}

		$conversation = $this->conversationRepo->findById($conversationId);
		if (!$conversation ||
			($conversation->getUserOneId() !== $_SESSION['user']['id'] &&
				$conversation->getUserTwoId() !== $_SESSION['user']['id'])) {
			throw new UnauthorizedException("Accès interdit à cette conversation.");
		}

		$isUserOne = false;
		
		$messages = $this->messageRepo->findMessagesByConversation($conversationId);

		$this->messageRepo->markAllAsReadByUser($conversationId, $_SESSION['user']['id']);

		$data = [
			'title' => 'Conversation',
			'additionalCss' => ['messaging.css'],
			'conversation' => $conversation,
			'messages' => $messages,
			'isUserOne' => $isUserOne,
			'errors' => [],
		];
		$this->view('messaging/view', $data);
	}

	public function sendAjax()
	{
		try {
			if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
				throw new MethodNotAllowedException("Méthode HTTP non autorisée.");
			}

			if (!isset($_SESSION['user'])) {
				throw new UnauthorizedException("Non autorisé.");
			}

			$input = json_decode(file_get_contents('php://input'), true);
			$conversationId = isset($input['conversation_id']) ? filter_var($input['conversation_id'], FILTER_VALIDATE_INT) : null;
			$messageContent = isset($input['message']) ? trim($input['message']) : '';

			if (!$conversationId || $messageContent === '') {
				throw new Exception("Paramètres manquants ou message vide.");
			}

			$conversation = $this->conversationRepo->findById($conversationId);
			if (!$conversation ||
				($conversation->getUserOneId() !== $_SESSION['user']['id'] &&
					$conversation->getUserTwoId() !== $_SESSION['user']['id'])) {
				throw new UnauthorizedException("Accès interdit à cette conversation.");
			}

			if (!$this->rateLimit->isAllowed('send_message_' . $_SESSION['user']['id'])) {
				throw new Exception("Trop de messages envoyés. Veuillez réessayer plus tard.");
			}

			$message = $this->messageRepo->createMessage(
				conversationId: (int)$conversationId,
				senderId: $_SESSION['user']['id'],
				content: $messageContent
			);

			echo json_encode(['success' => true, 'message_id' => $message->getId()]);
		} catch (MethodNotAllowedException $e) {
			http_response_code(405);
			echo json_encode(['error' => $e->getMessage()]);
		} catch (UnauthorizedException $e) {
			http_response_code(403);
			echo json_encode(['error' => $e->getMessage()]);
		} catch (Exception $e) {
			http_response_code(400);
			echo json_encode(['error' => $e->getMessage()]);
		}
		exit;
	}


	public function fetchMessages()
	{
		if (!isset($_SESSION['user'])) {
			http_response_code(401);
			echo json_encode(['error' => 'Non autorisé']);
			exit;
		}

		$conversationId = isset($_GET['conversation_id']) ? filter_var($_GET['conversation_id'], FILTER_VALIDATE_INT) : null;
		$lastMessageId = isset($_GET['last_message_id']) ? filter_var($_GET['last_message_id'], FILTER_VALIDATE_INT) : 0;

		if (!$conversationId) {
			http_response_code(400);
			echo json_encode(['error' => 'Paramètres manquants']);
			exit;
		}

		$conversation = $this->conversationRepo->findById($conversationId);
		if (!$conversation ||
			($conversation->getUserOneId() !== $_SESSION['user']['id'] &&
				$conversation->getUserTwoId() !== $_SESSION['user']['id'])) {
			http_response_code(403);
			echo json_encode(['error' => 'Accès interdit']);
			exit;
		}

		$newMessages = $this->messageRepo->findNewMessages($conversationId, $lastMessageId);

		$messagesData = [];
		foreach ($newMessages as $message) {
			$messagesData[] = [
				'id' => $message->getId(),
				'conversation_id' => $message->getConversationId(),
				'sender_id' => $message->getSenderId(),
				'content' => htmlspecialchars($message->getContent()),
				'sent_at' => $message->getSentAt(),
				'is_read' => $message->isRead(),
			];
		}

		echo json_encode(['messages' => $messagesData]);
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

		$conversationId = isset($_GET['conversation_id']) ? filter_var($_GET['conversation_id'], FILTER_VALIDATE_INT) : null;
		if (!$conversationId) {
			http_response_code(400);
			echo json_encode(['error' => 'Paramètres manquants']);
			exit;
		}

		$conversation = $this->conversationRepo->findById($conversationId);
		if (!$conversation ||
			($conversation->getUserOneId() !== $_SESSION['user']['id'] &&
				$conversation->getUserTwoId() !== $_SESSION['user']['id'])) {
			http_response_code(403);
			echo json_encode(['error' => 'Accès interdit']);
			exit;
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

		echo json_encode(['messages' => $messagesData]);
		exit;
	}

	public function countUnread()
	{
		try {
			if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
				throw new MethodNotAllowedException("Méthode HTTP non autorisée.");
			}

			if (!isset($_SESSION['user'])) {
				throw new UnauthorizedException("Non autorisé.");
			}

			$userId = $_SESSION['user']['id'];
			$count = $this->messageRepo->countNewMessages($userId);

			echo json_encode(['count' => $count]);
		} catch (MethodNotAllowedException $e) {
			http_response_code(405);
			echo json_encode(['error' => $e->getMessage()]);
		} catch (UnauthorizedException $e) {
			http_response_code(403);
			echo json_encode(['error' => $e->getMessage()]);
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode(['error' => 'Erreur interne du serveur.']);
		}
		exit;
	}


}
