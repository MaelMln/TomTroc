<header>
	<div class="header-container">
		<div class="logo">
			<img src="<?php echo $baseUrl; ?>/assets/img/logo.png" alt="Tom Troc Logo">
		</div>
		<ul class="nav-left">
			<li><a href="/">Accueil</a></li>
			<li><a href="/books">Nos livres à l'échange</a></li>
		</ul>
		<ul class="nav-right">
			<?php $isLoggedIn = isset($_SESSION['user']); ?>
			<?php if ($isLoggedIn): ?>
				<li>
					<a href="/messages">
						Messagerie
						<?php
						if (isset($newMessagesCount) && $newMessagesCount > 0) {
							echo "<span class='notification'>{$newMessagesCount}</span>";
						}
						?>
					</a>
				</li>
				<li><a href="/profile">Mon compte</a></li>
				<li><a href="/logout">Déconnexion</a></li>
			<?php else: ?>
				<li><a href="/login">Connexion</a></li>
				<li><a href="/register">Inscription</a></li>
			<?php endif; ?>
		</ul>
	</div>
</header>
