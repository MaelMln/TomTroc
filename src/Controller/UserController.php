<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ViewRenderer;
use App\Service\RateLimit;
use Exception;

class UserController extends AbstractController
{
	private RateLimit $rateLimit;

	public function __construct()
	{
		parent::__construct();
		$this->rateLimit = new RateLimit();
	}

	public function register()
	{
		$data = [
			'title' => 'Inscription',
			'additionalCss' => ['register.css'],
			'errors' => [],
		];

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			if (!$this->rateLimit->isAllowed('register_attempts')) {
				$data['errors'][] = 'Trop de tentatives d\'inscription. Veuillez réessayer plus tard.';
			} else {
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
			if (!$this->rateLimit->isAllowed('login_attempts')) {
				$data['errors'][] = 'Trop de tentatives de connexion. Veuillez réessayer plus tard.';
			} else {
				$email = trim($_POST['email'] ?? '');
				$password = $_POST['password'] ?? '';

				if (empty($email) || empty($password)) {
					$data['errors'][] = 'Veuillez remplir tous les champs.';
				} else {
					$userRepository = new UserRepository();
					$user = $userRepository->findByEmail($email);

					$dummyHash = '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';

					if ($user) {
						$passwordHash = $user->getPassword();
					} else {
						$passwordHash = $dummyHash;
					}

					if (password_verify($password, $passwordHash)) {
						if ($user) {
							$_SESSION['user'] = [
								'id' => $user->getId(),
								'username' => $user->getUsername(),
								'email' => $user->getEmail(),
							];
							header('Location: ' . $this->baseUrl);
							exit;
						}
					}

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
		$username = trim($input['username'] ?? '');
		$email = trim($input['email'] ?? '');
		$password = $input['password'] ?? '';

		if (empty($username) || empty($email) || empty($password)) {
			$errors[] = 'Tous les champs sont requis.';
			return $errors;
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'Adresse email invalide.';
		}

		if (strlen($password) < 6) {
			$errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
		}

		$userRepository = new UserRepository();
		$duplicate = false;

		if ($userRepository->findByEmail($email) || $userRepository->findByUsername($username)) {
			$duplicate = true;
		}

		if ($duplicate) {
			$errors[] = 'Un email de confirmation a été envoyé à votre adresse mail.';
			// Demande de validation par email. Si l'email est déjà présent dans la bdd, alors on informe de la tentative
			// de création de compte avec l'email déjà existant, sinon, réel message de confirmation d'inscription.
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
