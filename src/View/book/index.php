<div class="book-list">
	<h2>Nos livres disponibles à l'échange</h2>
	<form method="GET" action="<?php echo $baseUrl; ?>/books" class="search-form">
		<input type="text" name="search" placeholder="Rechercher par titre" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
		<button type="submit">Rechercher</button>
	</form>
	<a href="<?php echo $baseUrl; ?>/books/create" class="btn-create">Ajouter un nouveau livre</a>
	<div class="books">
		<?php if (empty($books)): ?>
			<p>Aucun livre disponible pour le moment.</p>
		<?php else: ?>
			<?php foreach ($books as $book): ?>
				<div class="book-item">
					<?php if ($book->getImage()): ?>
						<img src="<?php echo $baseUrl . $book->getImage(); ?>" alt="<?php echo htmlspecialchars($book->getTitle()); ?>">
					<?php endif; ?>
					<h3><?php echo htmlspecialchars($book->getTitle()); ?></h3>
					<p>Auteur : <?php echo htmlspecialchars($book->getAuthor()); ?></p>
					<a href="<?php echo $baseUrl; ?>/books/show?id=<?php echo $book->getId(); ?>" class="btn-view">Voir Détails</a>

					<?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] === $book->getUserId()): ?>
						<a href="<?php echo $baseUrl; ?>/books/edit?id=<?php echo $book->getId(); ?>" class="btn-edit">Modifier</a>

						<form method="POST" action="<?php echo $baseUrl; ?>/books/delete?id=<?php echo $book->getId(); ?>" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ? Cette action est irréversible.');">
							<button type="submit" class="btn-delete">Supprimer</button>
						</form>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>
