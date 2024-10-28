<?php

namespace App\Repository;

use App\Entity\User;

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
}
