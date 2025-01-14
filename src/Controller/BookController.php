<?php

namespace App\Controller;

use App\Entity\Book;
use App\Exception\MethodNotAllowedException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Repository\BookRepository;
use App\Service\RateLimit;
use Exception;

class BookController extends AbstractController
{
	private BookRepository $bookRepository;
	private RateLimit $rateLimit;

	public function __construct()
	{
		parent::__construct();
		$this->bookRepository = new BookRepository();
		$this->rateLimit = new RateLimit();
	}

	public function index()
	{
		$search = trim($_GET['search'] ?? '');
		$page = isset($_GET['page']) && filter_var($_GET['page'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) ? (int)$_GET['page'] : 1;
		$limit = 10;
		$offset = ($page - 1) * $limit;

		if ($search) {
			$books = $this->bookRepository->searchByTitle($search, $limit, $offset);
			$totalBooks = $this->bookRepository->countSearchByTitle($search);
		} else {
			$books = $this->bookRepository->findAllPaginated($limit, $offset);
			$totalBooks = $this->bookRepository->countAllAvailable();
		}

		$totalPages = ceil($totalBooks / $limit);

		$data = [
			'title' => 'Nos livres à l\'échange',
			'additionalCss' => ['book.css'],
			'books' => $books,
			'currentPage' => $page,
			'totalPages' => $totalPages,
			'search' => $search,
		];
		$this->view('book/index', $data);
	}




	public function show($id)
	{
		$id = filter_var($id, FILTER_VALIDATE_INT);
		if (!$id) {
			throw new NotFoundException("Livre non trouvé.");
		}

		$book = $this->bookRepository->findById($id);
		if (!$book) {
			throw new NotFoundException("Livre non trouvé.");
		}

		$data = [
			'title' => htmlspecialchars($book->getTitle()) . ' - TomTroc',
			'additionalCss' => ['book.css'],
			'book' => $book,
		];
		$this->view('book/show', $data);
	}



	public function create()
	{
		if (!isset($_SESSION['user'])) {
			header('Location: ' . $this->baseUrl . '/login');
			exit;
		}

		$this->handleBookForm();
	}

	public function edit($id)
	{
		if (!isset($_SESSION['user'])) {
			throw new UnauthorizedException("Vous devez être connecté pour modifier un livre.");
		}

		$id = filter_var($id, FILTER_VALIDATE_INT);
		if (!$id) {
			throw new NotFoundException("Livre non trouvé.");
		}

		$book = $this->bookRepository->findById($id);
		if (!$book || $book->getUserId() !== $_SESSION['user']['id']) {
			throw new UnauthorizedException("Vous n'avez pas l'autorisation de modifier ce livre.");
		}

		$this->handleBookForm($book);
	}


	public function delete($id)
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			throw new MethodNotAllowedException("Méthode non autorisée.");
		}

		if (!isset($_SESSION['user'])) {
			throw new UnauthorizedException("Vous devez être connecté pour supprimer un livre.");
		}

		$id = filter_var($id, FILTER_VALIDATE_INT);
		if (!$id) {
			throw new NotFoundException("Livre non trouvé.");
		}

		$book = $this->bookRepository->findById($id);
		if (!$book || $book->getUserId() !== $_SESSION['user']['id']) {
			throw new UnauthorizedException("Vous n'avez pas l'autorisation de supprimer ce livre.");
		}

		if ($this->bookRepository->delete((int)$id)) {
			$imagePath = $book->getImage();
			if ($imagePath) {
				$fullImagePath = ROOT_DIR . '/public' . $imagePath;
				if (file_exists($fullImagePath)) {
					unlink($fullImagePath);
				}
			}
			header('Location: ' . $this->baseUrl . '/books');
			exit;
		} else {
			$_SESSION['error'] = 'Une erreur est survenue lors de la suppression du livre.';
			header('Location: ' . $this->baseUrl . '/books/show/' . $id);
			exit;
		}
	}

	private function handleBookForm(?Book $book = null)
	{
		$isEdit = $book !== null;

		$data = [
			'title' => $isEdit ? 'Modifier le livre' : 'Ajouter un livre',
			'additionalCss' => ['book.css'],
			'errors' => [],
			'book' => $book,
		];

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$input = $_POST;
			$errors = $this->validateBookInput($input, $_FILES['image'] ?? null, $book);

			if (empty($errors)) {
				$imagePath = $book ? $book->getImage() : null;
				if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
					if ($book && $book->getImage()) {
						$fullImagePath = ROOT_DIR . '/public' . $book->getImage();
						if (file_exists($fullImagePath)) {
							unlink($fullImagePath);
						}
					}
					try {
						$imagePath = $this->handleImageUpload($_FILES['image']);
					} catch (Exception $e) {
						$errors[] = $e->getMessage();
					}
				}

				if (empty($errors)) {
					if ($isEdit) {
						$book->setTitle($input['title']);
						$book->setAuthor($input['author']);
						$book->setImage($imagePath);
						$book->setDescription($input['description'] ?? null);
						$book->setStatus($input['status'] ?? 'disponible');
						$book->setUpdatedAt(date('Y-m-d H:i:s'));

						if ($this->bookRepository->save($book)) {
							header('Location: ' . $this->baseUrl . '/books/show/' . $book->getId());
							exit;
						} else {
							$errors[] = 'Une erreur est survenue lors de la mise à jour du livre.';
						}
					} else {
						$newBook = new Book(
							id: null,
							userId: $_SESSION['user']['id'],
							title: $input['title'],
							author: $input['author'],
							image: $imagePath,
							description: $input['description'] ?? null,
							status: $input['status'] ?? 'disponible',
						);

						if ($this->bookRepository->save($newBook)) {
							header('Location: ' . $this->baseUrl . '/books');
							exit;
						} else {
							$errors[] = 'Une erreur est survenue lors de la création du livre.';
						}
					}
				}

				$data['errors'] = $errors;
			} else {
				$data['errors'] = $errors;
			}
		}

		$this->view($isEdit ? 'book/edit' : 'book/create', $data);
	}

	private function validateBookInput(array $input, $file = null, ?Book $existingBook = null): array
	{
		$errors = [];

		$title = trim($input['title'] ?? '');
		if (empty($title)) {
			$errors[] = 'Le titre est requis.';
		} elseif (strlen($title) > 255) {
			$errors[] = 'Le titre ne doit pas dépasser 255 caractères.';
		}

		$author = trim($input['author'] ?? '');
		if (empty($author)) {
			$errors[] = 'L\'auteur est requis.';
		} elseif (strlen($author) > 255) {
			$errors[] = 'Le nom de l\'auteur ne doit pas dépasser 255 caractères.';
		}

		$status = $input['status'] ?? 'disponible';
		if (!in_array($status, ['disponible', 'non_disponible'])) {
			$errors[] = 'Statut de disponibilité invalide.';
		}

		$description = trim($input['description'] ?? '');
		if (strlen($description) > 1000) {
			$errors[] = 'La description ne doit pas dépasser 1000 caractères.';
		}


		return $errors;
	}


	private function handleImageUpload(array $file): string
	{
		$uploadDir = ROOT_DIR . '/public/assets/uploads/';
		if (!is_dir($uploadDir)) {
			mkdir($uploadDir, 0755, true);
		}

		$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
		if (!in_array($extension, $allowedExtensions)) {
			throw new Exception('Type d\'image non supporté. Les types acceptés sont JPG, JPEG, PNG, GIF.');
		}

		$filename = uniqid() . '.' . $extension;
		$destination = $uploadDir . $filename;

		if (move_uploaded_file($file['tmp_name'], $destination)) {
			return '/assets/uploads/' . $filename;
		}

		throw new Exception('Erreur lors de l\'upload de l\'image.');
	}
}
