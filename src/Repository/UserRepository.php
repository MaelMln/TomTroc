<?php

namespace App\Repository;

use App\Entity\User;
use PDO;

class UserRepository extends AbstractRepository
{
	protected function getTableName(): string
	{
		return 'users';
	}

	protected function getEntityClass(): string
	{
		return User::class;
	}

	public function findByEmail(string $email): ?User
	{
		$stmt = $this->db->prepare("SELECT * FROM {$this->getTableName()} WHERE email = :email");
		$stmt->execute(['email' => $email]);
		$data = $stmt->fetch(PDO::FETCH_ASSOC);

		return $data ? $this->hydrate($data) : null;
	}

	public function findByUsername(string $username): ?User
	{
		$stmt = $this->db->prepare("SELECT * FROM {$this->getTableName()} WHERE username = :username");
		$stmt->execute(['username' => $username]);
		$data = $stmt->fetch(PDO::FETCH_ASSOC);

		return $data ? $this->hydrate($data) : null;
	}

	public function findById(int $id): ?User
	{
		$stmt = $this->db->prepare("SELECT * FROM {$this->getTableName()} WHERE id = :id");
		$stmt->execute(['id' => $id]);
		$data = $stmt->fetch(PDO::FETCH_ASSOC);

		return $data ? $this->hydrate($data) : null;
	}

}
