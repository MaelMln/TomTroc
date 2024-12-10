<div class="book-detail">
	<h1><?php echo htmlspecialchars($book->getTitle()); ?></h1>
	<p>Auteur : <?php echo htmlspecialchars($book->getAuthor()); ?></p>

	<?php if ($book->getImage()): ?>
		<img src="<?php echo $baseUrl . $book->getImage(); ?>" alt="<?php echo htmlspecialchars($book->getTitle()); ?>" class="book-image">
	<?php endif; ?>

	<p>Description : <?php echo nl2br(htmlspecialchars($book->getDescription())); ?></p>
	<p>Statut : <?php echo htmlspecialchars($book->getStatus()); ?></p>
	<p>Ajouté le : <?php echo htmlspecialchars($book->getCreatedAt()); ?></p>

	<?php if ($book->getUpdatedAt() !== null): ?>
		<p>Mise à jour le : <?php echo htmlspecialchars($book->getUpdatedAt()); ?></p>
	<?php endif; ?>

	<a href="<?php echo $baseUrl; ?>/users/show?id=<?php echo $book->getUserId(); ?>" class="btn-profile">Voir le profil du propriétaire</a>
	<a href="<?php echo $baseUrl; ?>/messages/send?to=<?php echo $book->getUserId(); ?>&book=<?php echo $book->getId(); ?>" class="btn-message">Envoyer un message</a>

	<?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] === $book->getUserId()): ?>
		<a href="<?php echo $baseUrl; ?>/books/edit?id=<?php echo $book->getId(); ?>" class="btn-edit">Modifier</a>
		<a href="<?php echo $baseUrl; ?>/books/delete?id=<?php echo $book->getId(); ?>" class="btn-delete">Supprimer</a>
	<?php endif; ?>
</div>