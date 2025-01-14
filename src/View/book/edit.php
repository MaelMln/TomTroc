<div>
	<h1>Modifier le livre</h1>

	<?php if (!empty($errors)): ?>
		<div class="errors">
			<ul>
				<?php foreach ($errors as $error): ?>
					<li><?php echo htmlspecialchars($error); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<form method="POST" action="<?php echo $baseUrl; ?>/books/edit/<?php echo $book->getId(); ?>" enctype="multipart/form-data" novalidate>
		<div class="form-group">
			<label for="title">Titre</label>
			<input type="text" id="title" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? $book->getTitle()); ?>" required>
		</div>
		<div class="form-group">
			<label for="author">Auteur</label>
			<input type="text" id="author" name="author" value="<?php echo htmlspecialchars($_POST['author'] ?? $book->getAuthor()); ?>" required>
		</div>
		<div class="form-group">
			<label for="image">Image (optionnel)</label>
			<?php if ($book->getImage()): ?>
				<img src="<?php echo $baseUrl . $book->getImage(); ?>" alt="<?php echo htmlspecialchars($book->getTitle()); ?>" class="current-image">
			<?php endif; ?>
			<input type="file" id="image" name="image" accept="image/*">
		</div>
		<div class="form-group">
			<label for="description">Description (optionnel)</label>
			<textarea id="description" name="description"><?php echo htmlspecialchars($_POST['description'] ?? $book->getDescription()); ?></textarea>
		</div>
		<div class="form-group">
			<label for="status">Statut</label>
			<select id="status" name="status">
				<option value="disponible" <?php echo ((($_POST['status'] ?? $book->getStatus()) === 'disponible') ? 'selected' : ''); ?>>Disponible</option>
				<option value="non_disponible" <?php echo ((($_POST['status'] ?? $book->getStatus()) === 'non_disponible') ? 'selected' : ''); ?>>Non Disponible</option>
			</select>
		</div>
		<button type="submit" class="btn-submit">Mettre à Jour le Livre</button>
	</form>
</div>
