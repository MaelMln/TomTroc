<?php

namespace App\Repository;

use App\Entity\Message;
use App\Exception\NotFoundException;
use PDO;

class MessageRepository extends AbstractRepository
{

	private ConversationRepository $conversationRepo;

	public function __construct()
	{
		parent::__construct();
		$this->conversationRepo = new ConversationRepository();
	}

	protected function getTableName(): string
	{
		return 'messages';
	}

	protected function getEntityClass(): string
	{
		return Message::class;
	}

	public function findMessagesByConversation(int $conversationId): array
	{
		$stmt = $this->db->prepare("SELECT * FROM {$this->getTableName()} WHERE conversation_id = :conversation_id ORDER BY sent_at ASC");
		$stmt->execute(['conversation_id' => $conversationId]);
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$messages = [];
		foreach ($data as $row) {
			$message = $this->hydrate($row);
			$messages[] = $message;
		}
		return $messages;
	}


	public function findNewMessages(int $conversationId, int $lastMessageId): array
	{
		$stmt = $this->db->prepare("
        SELECT * FROM messages 
        WHERE conversation_id = :conversation_id 
          AND id > :last_id 
        ORDER BY sent_at ASC
    ");
		$stmt->bindValue(':conversation_id', $conversationId, PDO::PARAM_INT);
		$stmt->bindValue(':last_id', $lastMessageId, PDO::PARAM_INT);
		$stmt->execute();
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$messages = [];
		foreach ($data as $row) {
			$messages[] = $this->hydrate($row);
		}
		return $messages;
	}

	public function createMessage(int $conversationId, int $senderId, string $content): Message
	{
		$message = new Message(
			id: null,
			conversationId: $conversationId,
			senderId: $senderId,
			content: $content,
			sentAt: date('Y-m-d H:i:s')
		);

		$lastInsertId = $this->save($message);

		$messageWithId = new Message(
			id: $lastInsertId,
			conversationId: $conversationId,
			senderId: $senderId,
			content: $content,
			sentAt: $message->getSentAt()
		);

		return $messageWithId;
	}

	public function countNewMessages(int $userId): int
	{
		$stmt = $this->db->prepare("
        SELECT COUNT(*) as count
          FROM messages m
          JOIN conversations c ON m.conversation_id = c.id
         WHERE (
               (c.user_one_id = :user_id AND m.is_read_by_user_one = 0)
            OR (c.user_two_id = :user_id AND m.is_read_by_user_two = 0)
         )
           AND m.sender_id != :user_id
    ");

		$stmt->execute(['user_id' => $userId]);
		$data = $stmt->fetch(PDO::FETCH_ASSOC);

		return $data ? (int)$data['count'] : 0;
	}


	public function markAllAsReadByUser(int $conversationId, int $userId): bool
	{
		$conversation = $this->conversationRepo->findById($conversationId);
		if (!$conversation) {
			throw new NotFoundException("Conversation non trouvée.");
		}

		if ($conversation->getUserOneId() === $userId) {
			$sql = "
            UPDATE messages
               SET is_read_by_user_one = 1
             WHERE conversation_id = :conversation_id
               AND sender_id != :user_id
        ";
		} else {
			$sql = "
            UPDATE messages
               SET is_read_by_user_two = 1
             WHERE conversation_id = :conversation_id
               AND sender_id != :user_id
        ";
		}

		$stmt = $this->db->prepare($sql);
		return $stmt->execute([
			'conversation_id' => $conversationId,
			'user_id' => $userId,
		]);
	}


}
