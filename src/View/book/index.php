<div class="book-list">
	<h2>Nos livres disponibles à l'échange</h2>
	<form method="GET" action="<?php echo $baseUrl; ?>/books" class="search-form">
		<input type="text" name="search" placeholder="Rechercher par titre"
			   value="<?php echo htmlspecialchars($search ?? ''); ?>">
		<button type="submit">Rechercher</button>
	</form>

	<div class="books">
		<?php if (empty($books)): ?>
			<p>Aucun livre disponible pour le moment.</p>
		<?php else: ?>
			<?php foreach ($books as $book): ?>
				<div class="book-item">
					<?php if ($book->getImage()): ?>
						<img src="<?php echo $baseUrl . $book->getImage(); ?>"
							 alt="<?php echo htmlspecialchars($book->getTitle()); ?>">
					<?php endif; ?>
					<h3><?php echo htmlspecialchars($book->getTitle()); ?></h3>
					<p>Auteur : <?php echo htmlspecialchars($book->getAuthor()); ?></p>

					<a href="<?php echo $baseUrl; ?>/books/show/<?php echo $book->getId(); ?>" class="btn-view">Voir Détails</a>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<?php if ($totalPages > 1): ?>
		<div class="pagination">
			<?php if ($currentPage > 1): ?>
				<a href="<?php echo $baseUrl; ?>/books?search=<?php echo urlencode($search); ?>&page=<?php echo $currentPage - 1; ?>" class="btn-prev">Précédent</a>
			<?php endif; ?>

			<span>Page <?php echo $currentPage; ?> sur <?php echo $totalPages; ?></span>

			<?php if ($currentPage < $totalPages): ?>
				<a href="<?php echo $baseUrl; ?>/books?search=<?php echo urlencode($search); ?>&page=<?php echo $currentPage + 1; ?>" class="btn-next">Suivant</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>
