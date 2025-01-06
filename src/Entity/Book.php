<?php

namespace App\Entity;

class Book
{
	private string $createdAt;
	private ?string $updatedAt = null;

	public function __construct(
		private ?int $id,
		private int $userId,
		private string $title,
		private string $author,
		private ?string $image,
		private ?string $description,
		private string $status,
	) {
		$this->createdAt = date('Y-m-d H:i:s');
	}


	public function getId(): ?int
	{
		return $this->id;
	}

	public function getUserId(): int
	{
		return $this->userId;
	}

	public function setUserId(int $userId): void
	{
		$this->userId = $userId;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function setTitle(string $title): void
	{
		$this->title = $title;
	}

	public function getAuthor(): string
	{
		return $this->author;
	}

	public function setAuthor(string $author): void
	{
		$this->author = $author;
	}

	public function getImage(): ?string
	{
		return $this->image;
	}

	public function setImage(?string $image): void
	{
		$this->image = $image;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function getStatus(): string
	{
		return $this->status;
	}

	public function setStatus(string $status): void
	{
		$this->status = $status;
	}

	public function getCreatedAt(): string
	{
		return $this->createdAt;
	}

	public function getUpdatedAt(): ?string
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(string $date): ?string
	{
		return $this->updatedAt = $date;
	}
	
}
