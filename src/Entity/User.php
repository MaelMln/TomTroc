<?php

namespace App\Entity;

class User
{

	private ?string $updatedAt = null;
	private string $createdAt;

	public function __construct(
		private ?int     $id,
		private string  $username,
		private string  $email,
		private string  $password,
		private ?string $fullName,
		private ?string $profilePicture,
	)
	{
		$this->createdAt = date(DATE_ATOM);
	}

	public function getId(): ?int
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

	public function getUpdatedAt(): ?string
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(?string $updatedAt): void
	{
		$this->updatedAt = $updatedAt;
	}
}