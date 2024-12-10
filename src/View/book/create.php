<div class="create-book-container">
	<h1>Ajouter un nouveau livre</h1>

	<?php if (!empty($errors)): ?>
		<div class="errors">
			<ul>
				<?php foreach ($errors as $error): ?>
					<li><?php echo htmlspecialchars($error); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<form method="POST" action="<?php echo $baseUrl; ?>/books/create" enctype="multipart/form-data" novalidate>
		<div class="form-group">
			<label for="title">Titre</label>
			<input type="text" id="title" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
		</div>
		<div class="form-group">
			<label for="author">Auteur</label>
			<input type="text" id="author" name="author" value="<?php echo htmlspecialchars($_POST['author'] ?? ''); ?>" required>
		</div>
		<div class="form-group">
			<label for="image">Image (optionnel)</label>
			<input type="file" id="image" name="image" accept="image/*">
		</div>
		<div class="form-group">
			<label for="description">Description (optionnel)</label>
			<textarea id="description" name="description"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
		</div>
		<div class="form-group">
			<label for="status">Statut</label>
			<select id="status" name="status">
				<option value="disponible" <?php echo (($_POST['status'] ?? '') === 'disponible') ? 'selected' : ''; ?>>Disponible</option>
				<option value="non_disponible" <?php echo (($_POST['status'] ?? '') === 'non_disponible') ? 'selected' : ''; ?>>Non Disponible</option>
			</select>
		</div>
		<button type="submit" class="btn-submit">Ajouter le Livre</button>
	</form>
</div>
