<?php

namespace App\Repository;

use App\Entity\Message;
use PDO;

class MessageRepository extends AbstractRepository
{
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
			$messages[] = $this->hydrate($row);
		}
		return $messages;
	}

	public function findNewMessages(int $conversationId, int $lastMessageId): array
	{
		$stmt = $this->db->prepare("SELECT * FROM {$this->getTableName()} WHERE conversation_id = :conversation_id AND id > :last_id ORDER BY sent_at ASC");
		$stmt->execute([
			'conversation_id' => $conversationId,
			'last_id' => $lastMessageId
		]);
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
        WHERE (c.user_one_id = :user_id OR c.user_two_id = :user_id)
        AND m.sender_id != :user_id
        AND m.id > (
            SELECT MAX(id) FROM messages
            WHERE conversation_id = m.conversation_id
            AND sender_id = :user_id
        )
    ");
		$stmt->execute(['user_id' => $userId]);
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return $data ? (int)$data['count'] : 0;
	}
}
