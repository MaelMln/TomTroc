<?php

namespace App\Repository;

use App\Entity\Book;
use PDO;
use Exception;

class BookRepository extends AbstractRepository
{
	protected function getTableName(): string
	{
		return 'books';
	}

	protected function getEntityClass(): string
	{
		return Book::class;
	}

	public function findByUserId(int $userId): array
	{
		$stmt = $this->db->prepare("SELECT * FROM {$this->getTableName()} WHERE user_id = :user_id");
		$stmt->execute(['user_id' => $userId]);
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$books = [];
		foreach ($data as $row) {
			$books[] = $this->hydrate($row);
		}
		return $books;
	}

	public function searchByTitle(string $title, int $limit, int $offset): array
	{
		$stmt = $this->db->prepare("
            SELECT * FROM {$this->getTableName()}
            WHERE title LIKE :title AND status = 'disponible'
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ");
		$stmt->bindValue(':title', '%' . $title . '%', PDO::PARAM_STR);
		$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
		$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
		$stmt->execute();
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$books = [];
		foreach ($data as $row) {
			$books[] = $this->hydrate($row);
		}
		return $books;
	}

	public function countByUserId(int $userId): int
	{
		$stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->getTableName()} WHERE user_id = :user_id");
		$stmt->execute(['user_id' => $userId]);
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return $data ? (int)$data['count'] : 0;
	}

	public function findAllPaginated(int $limit, int $offset): array
	{
		$stmt = $this->db->prepare("
            SELECT * FROM {$this->getTableName()}
            WHERE status = 'disponible'
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ");
		$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
		$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
		$stmt->execute();
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$books = [];
		foreach ($data as $row) {
			$books[] = $this->hydrate($row);
		}
		return $books;
	}

	public function countAllAvailable(): int
	{
		$stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM {$this->getTableName()}
            WHERE status = 'disponible'
        ");
		$stmt->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return $data ? (int)$data['count'] : 0;
	}

	public function countSearchByTitle(string $title): int
	{
		$stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM {$this->getTableName()}
            WHERE title LIKE :title AND status = 'disponible'
        ");
		$stmt->bindValue(':title', '%' . $title . '%', PDO::PARAM_STR);
		$stmt->execute();
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		return $data ? (int)$data['count'] : 0;
	}
}
