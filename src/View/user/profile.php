<div class="user-profile">
	<h1><?php echo htmlspecialchars($user->getUsername()); ?></h1>
	<p>Membre depuis : <?php echo date('d/m/Y', strtotime($user->getCreatedAt())); ?></p>
	<p>Nombre de livres : <?php echo $bookCount; ?></p>

	<?php if ($user->getProfilePicture()): ?>
		<img src="<?php echo $baseUrl . $user->getProfilePicture(); ?>"
			 alt="Photo de profil de <?php echo htmlspecialchars($user->getUsername()); ?>"
			 class="profile-picture">
	<?php endif; ?>

	<?php if ($isOwnProfile): ?>
		<div class="user-info">
			<h2>Vos informations personnelles</h2>

			<?php if (!empty($errors)): ?>
				<div class="errors">
					<ul>
						<?php foreach ($errors as $error): ?>
							<li><?php echo htmlspecialchars($error); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<form method="POST" action="<?php echo $baseUrl; ?>/profile/<?php echo $user->getId(); ?>"
				  enctype="multipart/form-data" novalidate>
				<div class="form-group">
					<label for="username">Pseudo</label>
					<input type="text" id="username" name="username"
						   value="<?php echo htmlspecialchars($_POST['username'] ?? $user->getUsername()); ?>" required>
				</div>
				<div class="form-group">
					<label for="email">Adresse email</label>
					<input type="email" id="email" name="email"
						   value="<?php echo htmlspecialchars($_POST['email'] ?? $user->getEmail()); ?>" required>
				</div>
				<div class="form-group">
					<label for="password">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
					<input type="password" id="password" name="password">
				</div>
				<div class="form-group">
					<label for="profile_picture">Photo de profil (optionnel)</label>
					<?php if ($user->getProfilePicture()): ?>
						<img src="<?php echo $baseUrl . $user->getProfilePicture(); ?>"
							 alt="Photo de profil"
							 class="current-profile-picture">
					<?php endif; ?>
					<input type="file" id="profile_picture" name="profile_picture" accept="image/*">
				</div>
				<button type="submit" class="btn-submit">Mettre à Jour le Profil</button>
			</form>
			<a href="<?php echo $baseUrl; ?>/books/create" class="btn-create-book">Ajouter un Livre</a>
		</div>
	<?php elseif (isset($_SESSION['user'])): ?>
		<a href="<?= $baseUrl ?>/conversation/start/<?= $user->getId() ?>" class="btn-message">
			Contacter cet utilisateur
		</a>
	<?php endif; ?>

	<div class="user-books">
		<h2>Livres de <?php echo htmlspecialchars($user->getUsername()); ?></h2>

		<?php if (!empty($books)): ?>
			<table class="books-table">
				<thead>
				<tr>
					<th>Photo</th>
					<th>Titre</th>
					<th>Auteur</th>
					<th>Description</th>
					<?php if ($isOwnProfile): ?>
						<th>Disponibilité</th>
						<th>Action</th>
					<?php endif; ?>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($books as $book): ?>
					<tr>
						<td>
							<?php if ($book->getImage()): ?>
								<img src="<?php echo $baseUrl . $book->getImage(); ?>"
									 alt="<?php echo htmlspecialchars($book->getTitle()); ?>"
									 class="book-cover-small">
							<?php endif; ?>
						</td>
						<td><?php echo htmlspecialchars($book->getTitle()); ?></td>
						<td><?php echo htmlspecialchars($book->getAuthor()); ?></td>
						<td class="description-cell">
							<?php echo htmlspecialchars($book->getDescription()); ?>
						</td>
						<?php if ($isOwnProfile): ?>
							<td>
								<?php echo ($book->getStatus() === 'disponible')
									? '<span class="badge badge-green">Disponible</span>'
									: '<span class="badge badge-red">Non disponible</span>'; ?>
							</td>
							<td>
								<a href="<?php echo $baseUrl; ?>/books/edit/<?php echo $book->getId(); ?>"
								   class="btn-edit">Modifier</a>
								<form method="POST"
									  action="<?php echo $baseUrl; ?>/books/delete/<?php echo $book->getId(); ?>"
									  style="display:inline-block"
									  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?');">
									<button type="submit" class="btn-delete">Supprimer</button>
								</form>
							</td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php else: ?>
			<p><?php echo htmlspecialchars($user->getUsername()); ?> ne possède aucun livre pour le moment.</p>
		<?php endif; ?>
	</div>
</div>