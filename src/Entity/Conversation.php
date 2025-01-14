<?php

namespace App\Entity;

class Conversation
{
	private string $createdAt;
	private ?string $updatedAt = null;
	private ?array $otherUser = null;

	private string $lastMessage = '';
	private string $lastSentAt = '';

	public function __construct(
		private ?int $id,
		private int $userOneId,
		private int $userTwoId
	) {
		$this->createdAt = date('Y-m-d H:i:s');
	}


	public function getId(): ?int
	{
		return $this->id;
	}

	public function getUserOneId(): int
	{
		return $this->userOneId;
	}

	public function setUserOneId(int $userOneId): self
	{
		$this->userOneId = $userOneId;
		return $this;
	}

	public function getUserTwoId(): int
	{
		return $this->userTwoId;
	}

	public function setUserTwoId(int $userTwoId): self
	{
		$this->userTwoId = $userTwoId;
		return $this;
	}

	public function getCreatedAt(): string
	{
		return $this->createdAt;
	}

	public function getUpdatedAt(): ?string
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(string $date): self
	{
		$this->updatedAt = $date;
		return $this;
	}

	public function getOtherUser(): ?array
	{
		return $this->otherUser;
	}

	public function setOtherUser(array $otherUser): self
	{
		$this->otherUser = $otherUser;
		return $this;
	}

	public function getLastMessage(): string
	{
		return $this->lastMessage;
	}

	public function setLastMessage(string $lastMessage): self
	{
		$this->lastMessage = $lastMessage;
		return $this;
	}

	public function getLastSentAt(): string
	{
		return $this->lastSentAt;
	}

	public function setLastSentAt(string $lastSentAt): self
	{
		$this->lastSentAt = $lastSentAt;
		return $this;
	}
}
