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
						<li>
							<a href="/messages">
								Messagerie<?php
								if (isset($newMessagesCount) && $newMessagesCount > 0) {
									echo " <span class='notification'>{$newMessagesCount}</span>";
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
		</nav>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			function updateUnreadCount() {
				fetch('<?php echo $baseUrl; ?>/messages/count_unread')
					.then(response => response.json())
					.then(data => {
						const messagerieLink = document.querySelector('nav .nav-right a[href="/messages"]');
						if (!messagerieLink) return;

						let notificationSpan = messagerieLink.querySelector('.notification');

						if (data.count > 0) {
							if (!notificationSpan) {
								notificationSpan = document.createElement('span');
								notificationSpan.classList.add('notification');
								messagerieLink.appendChild(notificationSpan);
							}
							notificationSpan.textContent = data.count;
						} else {
							if (notificationSpan) {
								messagerieLink.removeChild(notificationSpan);
							}
						}
					})
					.catch(error => {
						console.error('Erreur lors de la mise à jour des messages non lus:', error);
					});
			}

			updateUnreadCount();

			setInterval(updateUnreadCount, 5000);
		});
	</script>

</header>