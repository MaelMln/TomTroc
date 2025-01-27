<?php

namespace App\Controller;

use App\Repository\ConversationRepository;
use App\Exception\UnauthorizedException;
use App\Exception\NotFoundException;

class ConversationController extends AbstractController
{
	private ConversationRepository $conversationRepo;

	public function __construct()
	{
		parent::__construct();
		$this->conversationRepo = new ConversationRepository();
	}

	public function startConversation($to_user_id)
	{
		if (!isset($_SESSION['user'])) {
			throw new UnauthorizedException("Vous devez être connecté pour démarrer une conversation.");
		}

		$fromUserId = $_SESSION['user']['id'];
		$toUserId = filter_var($to_user_id, FILTER_VALIDATE_INT);

		if (!$toUserId) {
			throw new NotFoundException("Utilisateur destinataire non trouvé.");
		}

		$userRepo = new \App\Repository\UserRepository();
		$recipient = $userRepo->findById($toUserId);
		if (!$recipient) {
			throw new NotFoundException("Utilisateur destinataire non trouvé.");
		}

		$conversation = $this->conversationRepo->findConversation($fromUserId, $toUserId);
		if (!$conversation) {
			$conversation = $this->conversationRepo->createConversation($fromUserId, $toUserId);
		}

		header('Location: ' . $this->baseUrl . '/messages/view/' . $conversation->getId());
		exit;
	}

}
