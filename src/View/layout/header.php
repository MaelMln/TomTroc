<header>
	<div class="container">
		<div class="logo">
			<img src="<?php echo $baseUrl; ?>/assets/img/logo.png" alt="Tom Troc Logo">
		</div>
		<nav>
			<div class="nav-left">
				<ul>
					<li><a href="/">Accueil</a></li>
					<li><a href="/books">Nos livres à l'échange</a></li>
				</ul>
			</div>
			<div class="nav-right">
				<ul>
					<?php
					$isLoggedIn = isset($_SESSION['user']);
					?>
					<?php if ($isLoggedIn): ?>
						<li><a href="/account">Mon compte</a></li>
						<li><a href="/logout">Déconnexion</a></li>
					<?php else: ?>
						<li><a href="/login">Connexion</a></li>
						<li><a href="/register">Inscription</a></li>
					<?php endif; ?>
				</ul>
			</div>
		</nav>
	</div>
</header>
