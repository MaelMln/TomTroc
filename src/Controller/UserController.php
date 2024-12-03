<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;

class UserController extends AbstractController
{
	public function register()
	{
		$data = [
			'title' => 'Inscription',
			'additionalCss' => ['register.css'],
			'errors' => [],
		];

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$data['errors'] = $this->validateRegistration($_POST);

			if (empty($data['errors'])) {
				$user = $this->createUser($_POST);
				$userRepository = new UserRepository();

				if ($userRepository->save($user)) {
					header('Location: ' . $this->baseUrl . '/login');
					exit;
				} else {
					$data['errors'][] = 'Une erreur est survenue lors de l\'inscription.';
				}
			}
		}

		$this->view('user/register', $data);
	}

	public function login()
	{
		$data = [
			'title' => 'Connexion',
			'additionalCss' => ['login.css'],
			'errors' => [],
		];

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$email = $_POST['email'] ?? null;
			$password = $_POST['password'] ?? null;

			if (!$email || !$password) {
				$data['errors'][] = 'Veuillez remplir tous les champs.';
			} else {
				$userRepository = new UserRepository();
				$user = $userRepository->findByEmail($email);

				if ($user && password_verify($password, $user->getPassword())) {
					$_SESSION['user'] = [
						'id' => $user->getId(),
						'username' => $user->getUsername(),
						'email' => $user->getEmail(),
					];
					header('Location: ' . $this->baseUrl);
					exit;
				} else {
					$data['errors'][] = 'Email ou mot de passe incorrect.';
				}
			}
		}

		$this->view('user/login', $data);
	}

	public function logout()
	{
		session_destroy();
		header('Location: ' . $this->baseUrl . '/login');
		exit;
	}


	private function validateRegistration(array $input): array
	{
		$errors = [];
		$username = $input['username'] ?? null;
		$email = $input['email'] ?? null;
		$password = $input['password'] ?? null;

		if (!$username || !$email || !$password) {
			$errors[] = 'Tous les champs sont requis.';
		}

		if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'Email invalide.';
		}

		if ($password && strlen($password) < 6) {
			$errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
		}

		return $errors;
	}

	private function createUser(array $input): User
	{
		return new User(
			id: null,
			username: $input['username'] ?? '',
			email: $input['email'] ?? '',
			password: password_hash($input['password'], PASSWORD_BCRYPT),
			fullName: null,
			profilePicture: null,
		);
	}
}
