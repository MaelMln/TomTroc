<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\BookRepository;
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

	public function profile()
	{
		$userRepository = new UserRepository();
		$bookRepository = new BookRepository();

		$id = $_GET['id'] ?? null;

		if (!$id) {
			if (!isset($_SESSION['user'])) {
				header('Location: ' . $this->baseUrl . '/login');
				exit;
			}
			$id = $_SESSION['user']['id'];
		}

		$user = $userRepository->findById((int)$id);

		if (!$user) {
			header('Location: ' . $this->baseUrl);
			exit;
		}

		$bookCount = $bookRepository->countByUserId((int)$id);

		$books = $bookRepository->findByUserId((int)$id);

		$isOwnProfile = isset($_SESSION['user']) && $_SESSION['user']['id'] === $user->getId();

		$data = [
			'title' => htmlspecialchars($user->getUsername()) . ' - TomTroc',
			'additionalCss' => ['profile.css'],
			'user' => $user,
			'bookCount' => $bookCount,
			'books' => $books,
			'isOwnProfile' => $isOwnProfile,
			'errors' => [],
		];

		if ($isOwnProfile && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$input = $_POST;
			$errors = $this->validateProfileInput($input, $user, $_FILES['profile_picture'] ?? null);

			if (empty($errors)) {
				$user->setUsername($input['username']);
				$user->setEmail($input['email']);

				if (!empty($input['password'])) {
					$user->setPassword(password_hash($input['password'], PASSWORD_BCRYPT));
				}

				if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
					try {
						$profilePicturePath = $this->handleImageUpload($_FILES['profile_picture'], 'profile_pictures');
						$user->setProfilePicture($profilePicturePath);
					} catch (Exception $e) {
						$errors[] = $e->getMessage();
					}
				}

				if (empty($errors)) {
					if ($userRepository->save($user)) {
						$_SESSION['user']['username'] = $user->getUsername();
						$_SESSION['user']['email'] = $user->getEmail();
						header('Location: ' . $this->baseUrl . '/profile?id=' . $user->getId());
						exit;
					} else {
						$errors[] = 'Une erreur est survenue lors de la mise à jour de votre profil.';
					}
				}
			}

			$data['errors'] = $errors;
		}

		$this->view('user/profile', $data);
	}

	private function validateProfileInput(array $input, User $existingUser, $file = null): array
	{
		$errors = [];

		$username = trim($input['username'] ?? '');
		$email = trim($input['email'] ?? '');
		$password = $input['password'] ?? '';

		if (empty($username)) {
			$errors[] = 'Le pseudo est requis.';
		} else {
			$userRepository = new UserRepository();
			$existing = $userRepository->findByUsername($username);
			if ($existing && $existing->getId() !== $existingUser->getId()) {
				$errors[] = 'Ce pseudo est déjà pris.';
			}
		}

		if (empty($email)) {
			$errors[] = 'L\'adresse email est requise.';
		} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$errors[] = 'Adresse email invalide.';
		} else {
			$userRepository = new UserRepository();
			$existing = $userRepository->findByEmail($email);
			if ($existing && $existing->getId() !== $existingUser->getId()) {
				$errors[] = 'Cet email est déjà utilisé.';
			}
		}

		if (!empty($password) && strlen($password) < 6) {
			$errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
		}

		if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
			if ($file['error'] !== UPLOAD_ERR_OK) {
				$errors[] = 'Erreur lors de l\'upload de la photo de profil.';
			} else {
				$allowedMimeTypes = ['image/jpeg', 'image/png'];
				if (!in_array(mime_content_type($file['tmp_name']), $allowedMimeTypes)) {
					$errors[] = 'Type d\'image non supporté. Les types acceptés sont JPEG, PNG.';
				}
				if ($file['size'] > 2 * 1024 * 1024) {
					$errors[] = 'La photo de profil ne doit pas dépasser 2Mo.';
				}
			}
		}

		return $errors;
	}

	private function handleImageUpload(array $file, string $subDir = ''): string
	{
		$uploadDir = ROOT_DIR . '/public/assets/uploads/' . $subDir . '/';
		if (!is_dir($uploadDir)) {
			mkdir($uploadDir, 0755, true);
		}

		$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		$allowedExtensions = ['jpg', 'jpeg', 'png'];
		if (!in_array($extension, $allowedExtensions)) {
			throw new Exception('Type d\'image non supporté. Les types acceptés sont JPG, JPEG, PNG.');
		}

		$filename = uniqid() . '.' . $extension;
		$destination = $uploadDir . $filename;

		if (move_uploaded_file($file['tmp_name'], $destination)) {
			return '/assets/uploads/' . $subDir . '/' . $filename;
		}

		throw new Exception('Erreur lors de l\'upload de l\'image.');
	}
}
