<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;

class UserController extends AbstractController
{
	public function register()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$username = $_POST['username'] ?? null;
			$email = $_POST['email'] ?? null;
			$password = $_POST['password'] ?? null;

			$errors = [];

			if (!$username || !$email || !$password) {
				$errors[] = 'Tous les champs sont requis.';
			}

			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$errors[] = 'Email invalide.';
			}

			if (strlen($password) < 6) {
				$errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
			}

			if (!empty($errors)) {
				$data = [
					'errors' => $errors,
					'title' => 'Inscription',
					'additionalCss' => ['user.css'],
				];
				$this->view('user/register', $data);
				return;
			}

			$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

			$user = new User(
				id: 0,
				username: $username,
				email: $email,
				password: $hashedPassword,
				fullName: null,
				profilePicture: null,
				createdAt: date('Y-m-d H:i:s')
			);

			$userRepository = new UserRepository();
			$result = $userRepository->save($user);

			if ($result) {
				header('Location: ' . $this->baseUrl . '/login');
				exit;
			} else {
				$errors[] = 'Une erreur est survenue lors de l\'inscription.';
				$data = [
					'errors' => $errors,
					'title' => 'Inscription',
					'additionalCss' => ['user.css'],
				];
				$this->view('user/register', $data);
			}
		} else {
			$data = [
				'title' => 'Inscription',
				'additionalCss' => ['user.css'],
			];
			$this->view('user/register', $data);
		}
	}
}
