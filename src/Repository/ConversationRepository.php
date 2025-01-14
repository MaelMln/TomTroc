<?php

namespace App\Repository;

use App\Entity\Conversation;
use PDO;

class ConversationRepository extends AbstractRepository
{
	protected function getTableName(): string
	{
		return 'conversations';
	}

	protected function getEntityClass(): string
	{
		return Conversation::class;
	}

	public function findConversation(int $userOneId, int $userTwoId): ?Conversation
	{
		$userOneId = min($userOneId, $userTwoId);
		$userTwoId = max($userOneId, $userTwoId);

		$stmt = $this->db->prepare("SELECT * FROM {$this->getTableName()} WHERE user_one_id = :user_one_id AND user_two_id = :user_two_id");
		$stmt->execute([
			'user_one_id' => $userOneId,
			'user_two_id' => $userTwoId
		]);
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return $data ? $this->hydrate($data) : null;
	}

	public function createConversation(int $userOneId, int $userTwoId): Conversation
	{
		$userOneId = min($userOneId, $userTwoId);
		$userTwoId = max($userOneId, $userTwoId);

		$conversation = new Conversation(
			id: null,
			userOneId: $userOneId,
			userTwoId: $userTwoId
		);

		$lastInsertId = $this->save($conversation);

		$conversationWithId = new Conversation(
			id: $lastInsertId,
			userOneId: $userOneId,
			userTwoId: $userTwoId
		);

		return $conversationWithId;
	}

	public function findAllConversationsWithUsersByUserId(int $userId, int $limit = 10, int $offset = 0): array
	{
		$stmt = $this->db->prepare("
        SELECT c.*, m.content AS last_message, m.sent_at AS last_sent_at,
               u.id AS user_id, u.username, u.profile_picture
        FROM {$this->getTableName()} c
        LEFT JOIN (
            SELECT conversation_id, content, sent_at
            FROM messages
            WHERE id IN (
                SELECT MAX(id) FROM messages GROUP BY conversation_id
            )
        ) m ON c.id = m.conversation_id
        JOIN users u ON (u.id = CASE
                                    WHEN c.user_one_id = :user_id THEN c.user_two_id
                                    ELSE c.user_one_id
                                END)
        WHERE c.user_one_id = :user_id OR c.user_two_id = :user_id
        ORDER BY m.sent_at DESC
        LIMIT :limit OFFSET :offset
    ");
		$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
		$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
		$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
		$stmt->execute();
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$conversations = [];
		foreach ($data as $row) {
			$conversation = $this->hydrate($row);
			$conversation->setOtherUser([
				'id' => $row['user_id'],
				'username' => $row['username'],
				'profile_picture' => $row['profile_picture']
			]);
			$conversation->setLastMessage($row['last_message'] ?? '');
			$conversation->setLastSentAt($row['last_sent_at'] ?? '');
			$conversations[] = $conversation;
		}
		return $conversations;
	}

	public function countAllConversationsByUserId(int $userId): int
	{
		$stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->getTableName()} WHERE user_one_id = :user_id OR user_two_id = :user_id");
		$stmt->execute(['user_id' => $userId]);
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return $data ? (int)$data['count'] : 0;
	}
}
