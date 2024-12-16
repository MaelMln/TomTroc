<?php

namespace App\Repository;

use App\Service\Database;
use PDO;
use App\Util\TextReformat;
use ReflectionClass;


abstract class AbstractRepository
{
	protected PDO $db;

	public function __construct()
	{
		$this->db = Database::getInstance();
	}

	protected abstract function getTableName(): string;
	protected abstract function getEntityClass(): string;

	public function findAll(): array
	{
		$stmt = $this->db->query("SELECT * FROM {$this->getTableName()}");
		$entities = [];
		while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$entities[] = $this->hydrate($data);
		}
		return $entities;
	}

	public function findById(int $id): ?object
	{
		$stmt = $this->db->prepare("SELECT * FROM {$this->getTableName()} WHERE id = :id");
		$stmt->execute(['id' => $id]);
		$data = $stmt->fetch(PDO::FETCH_ASSOC);

		return $data ? $this->hydrate($data) : null;
	}

	public function save(object $entity): bool
	{
		$data = $this->extract($entity);
		$table = $this->getTableName();
		if ($data['id'] !== null) {
			$setFields = array_map(fn($key) => "$key = :$key", array_keys($data));
			$sql = "UPDATE $table SET " . implode(', ', $setFields) . " WHERE id = :id";
		} else {
			$fields = implode(', ', array_keys($data));
			$placeholders = implode(', ', array_map(fn($key) => ":$key", array_keys($data)));
			$sql = "INSERT INTO $table ($fields) VALUES ($placeholders)";
		}

		$stmt = $this->db->prepare($sql);
		return $stmt->execute($data);
	}

	public function delete(int $id): bool
	{
		$stmt = $this->db->prepare("DELETE FROM {$this->getTableName()} WHERE id = :id");
		return $stmt->execute(['id' => $id]);
	}

	protected function hydrate(array $data): object
	{
		$className = $this->getEntityClass();
		$reflectionClass = new ReflectionClass($className);
		$entity = $reflectionClass->newInstanceWithoutConstructor();

		foreach ($data as $column => $value) {
			$propertyName = TextReformat::snakeToCamel($column);
			if (!$reflectionClass->hasProperty($propertyName)) {
				continue;
			}

			$property = $reflectionClass->getProperty($propertyName);
			$property->setValue($entity, $value);
		}

		return $entity;
	}

	private function extract(object $entity): array
	{
		$reflect = new \ReflectionClass($entity);
		$data = [];

		foreach ($reflect->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
			if (str_starts_with($method->getName(), 'get')) {
				$property = lcfirst(substr($method->getName(), 3));
				$column = TextReformat::camelToSnake($property);
				$value = $method->invoke($entity);

				if ($property === 'createdAt') {
					continue;
				}

				$data[$column] = $value;
			}
		}
		return $data;
	}
}
