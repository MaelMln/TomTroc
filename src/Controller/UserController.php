<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\BookRepository;
use App\Service\RateLimit;
use App\Service\ImageUploadService;
use App\Service\AuthService;
use Exception;

class UserController extends AbstractController
{
	private RateLimit $rateLimit;
	private ImageUploadService $imageService;

	public function __construct()
	{
		parent::__construct();
		$this->rateLimit = new RateLimit();
		$this->imageService = new ImageUploadService(ROOT_DIR);
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
				$filters = [
					'email' => FILTER_VALIDATE_EMAIL,
					'password' => FILTER_UNSAFE_RAW,
				];
				$filteredInput = filter_input_array(INPUT_POST, $filters);

				if ($filteredInput['email'] === false) {
					$data['errors'][] = 'Adresse email invalide.';
				}

				$password = $_POST['password'] ?? '';
				if (strlen($password) < 6) {
					$data['errors'][] = 'Le mot de passe doit contenir au moins 6 caractères.';
				}

				$username = trim(htmlspecialchars( $_POST['username'] ?? ''));
				if (empty($username)) {
					$data['errors'][] = 'Le pseudo est requis.';
				}

				$userRepository = new UserRepository();

				if ($userRepository->findByEmail($filteredInput['email']) || $userRepository->findByUsername($username)) {
					$data['errors'][] = 'Ce pseudo ou email est déjà utilisé.';
				}

				if (empty($data['errors'])) {
					$user = $this->createUser($filteredInput);
					$user->setUsername($username);
					$user->setPassword(password_hash($password, PASSWORD_BCRYPT));

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
					$passwordHash = $user ? $user->getPassword() : $dummyHash;

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

	public function profile($id = null)
	{
		$userRepository = new UserRepository();
		$bookRepository = new BookRepository();

		if ($id === null) {
			AuthService::ensureUserLoggedIn();
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
						if ($user->getProfilePicture()) {
							$this->imageService->delete($user->getProfilePicture());
						}
						$profilePicturePath = $this->imageService->upload($_FILES['profile_picture'], 'profile_pictures');
						$user->setProfilePicture($profilePicturePath);
					} catch (Exception $e) {
						$errors[] = $e->getMessage();
					}
				}

				if (empty($errors)) {
					if ($userRepository->save($user)) {
						$_SESSION['user']['username'] = $user->getUsername();
						$_SESSION['user']['email'] = $user->getEmail();
						header('Location: ' . $this->baseUrl . '/profile/' . $user->getId());
						exit;
					} else {
						$errors[] = 'Erreur lors de la mise à jour du profil.';
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
		if (empty($username)) {
			$errors[] = 'Le pseudo est requis.';
		} else {
			$userRepository = new UserRepository();
			$existing = $userRepository->findByUsername($username);
			if ($existing && $existing->getId() !== $existingUser->getId()) {
				$errors[] = 'Ce pseudo est déjà pris.';
			}
		}

		$email = trim($input['email'] ?? '');
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

		$password = $input['password'] ?? '';
		if (!empty($password) && strlen($password) < 6) {
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
			password: '',
			fullName: null,
			profilePicture: null
		);
	}
}
