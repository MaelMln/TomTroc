<div class="error-container">
	<h1><?php echo htmlspecialchars($statusCode); ?></h1>
	<p>
		<?php
		switch ($statusCode) {
			case 403:
				echo 'Accès interdit. Veuillez vous connecter pour accéder à cette page.';
				break;
			case 404:
				echo 'La page que vous recherchez est introuvable.';
				break;
			case 405:
				echo 'Méthode non autorisée.';
				break;
			default:
				echo 'Une erreur est survenue. Veuillez réessayer plus tard.';
		}
		?>
	</p>
	<?php if (isset($exception) && $config['env'] === 'development'): ?>
		<pre><?php echo htmlspecialchars($exception->getMessage() . "\n" . $exception->getTraceAsString()); ?></pre>
	<?php endif; ?>
	<p><a href="<?php echo htmlspecialchars($baseUrl); ?>">Retour à l'accueil</a></p>
</div>
