<?php

namespace App\Entity;

class Message
{
	private string $createdAt;
	private ?string $updatedAt = null;
	private bool $isReadByUserOne = false;
	private bool $isReadByUserTwo = false;

	public function __construct(
		private ?int $id,
		private int $conversationId,
		private int $senderId,
		private string $content,
		private string $sentAt
	) {
		$this->createdAt = date('Y-m-d H:i:s');
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getConversationId(): int
	{
		return $this->conversationId;
	}

	public function setConversationId(int $conversationId): void
	{
		$this->conversationId = $conversationId;
	}

	public function getSenderId(): int
	{
		return $this->senderId;
	}

	public function setSenderId(int $senderId): void
	{
		$this->senderId = $senderId;
	}

	public function getContent(): string
	{
		return $this->content;
	}

	public function setContent(string $content): void
	{
		$this->content = $content;
	}

	public function getSentAt(): string
	{
		return $this->sentAt;
	}

	public function setSentAt(string $sentAt): void
	{
		$this->sentAt = $sentAt;
	}

	public function getCreatedAt(): string
	{
		return $this->createdAt;
	}

	public function getUpdatedAt(): ?string
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(string $updatedAt): void
	{
		$this->updatedAt = $updatedAt;
	}

	public function isReadByUserOne(): bool
	{
		return $this->isReadByUserOne;
	}

	public function setReadByUserOne(bool $isRead): void
	{
		$this->isReadByUserOne = $isRead;
	}

	public function isReadByUserTwo(): bool
	{
		return $this->isReadByUserTwo;
	}

	public function setReadByUserTwo(bool $isRead): void
	{
		$this->isReadByUserTwo = $isRead;
	}
}
