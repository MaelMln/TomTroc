<div class="delete-book-container">
	<h1>Supprimer le livre</h1>

	<?php if (!empty($errors)): ?>
		<div class="errors">
			<ul>
				<?php foreach ($errors as $error): ?>
					<li><?php echo htmlspecialchars($error); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<p>Êtes-vous sûr de vouloir supprimer le livre "<?php echo htmlspecialchars($book->getTitle()); ?>" ? Cette action est irréversible.</p>

	<form method="POST" action="<?php echo $baseUrl; ?>/books/delete?id=<?php echo $book->getId(); ?>">
		<button type="submit" class="btn-delete">Supprimer</button>
		<a href="<?php echo $baseUrl; ?>/books" class="btn-cancel">Annuler</a>
	</form>
</div>
