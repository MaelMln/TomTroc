<?php

namespace App\Entity;

class User
{
	private int $id;
	private string $username;
	private string $email;
	private string $password;
	private ?string $fullName;
	private ?string $profilePicture;
	private string $createdAt;
	private string $updatedAt;

	public function __construct(
		string  $username,
		string  $email,
		string  $password,
		?string $fullName = null,
		?string $profilePicture = null,
		string  $createdAt = '',
		string  $updatedAt = ''
	)
	{
		$this->username = $username;
		$this->email = $email;
		$this->password = $password;
		$this->fullName = $fullName;
		$this->profilePicture = $profilePicture;
		$this->createdAt = $createdAt ?: date('Y-m-d H:i:s');
		$this->updatedAt = $updatedAt ?: date('Y-m-d H:i:s');
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function setId(int $id): void
	{
		$this->id = $id;
	}

	public function getUsername(): string
	{
		return $this->username;
	}

	public function setUsername(string $username): void
	{
		$this->username = $username;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function setEmail(string $email): void
	{
		$this->email = $email;
	}

	public function getPassword(): string
	{
		return $this->password;
	}

	public function setPassword(string $password): void
	{
		$this->password = $password;
	}

	public function getFullName(): ?string
	{
		return $this->fullName;
	}

	public function setFullName(?string $fullName): void
	{
		$this->fullName = $fullName;
	}

	public function getProfilePicture(): ?string
	{
		return $this->profilePicture;
	}

	public function setProfilePicture(?string $profilePicture): void
	{
		$this->profilePicture = $profilePicture;
	}

	public function getCreatedAt(): string
	{
		return $this->createdAt;
	}

	public function setCreatedAt(string $createdAt): void
	{
		$this->createdAt = $createdAt;
	}

	public function getUpdatedAt(): string
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(string $updatedAt): void
	{
		$this->updatedAt = $updatedAt;
	}
}
