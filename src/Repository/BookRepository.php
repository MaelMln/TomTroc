<?php

namespace App\Repository;

use App\Entity\Book;
use PDO;

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

	public function searchByTitle(string $title): array
	{
		$stmt = $this->db->prepare("SELECT * FROM {$this->getTableName()} WHERE title LIKE :title AND status = 'disponible'");
		$stmt->execute(['title' => '%' . $title . '%']);
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
}
