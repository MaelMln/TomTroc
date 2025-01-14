<div class="messaging-container">
	<div class="sidebar">
		<h2>Conversations</h2>
		<ul class="conversation-list">
			<?php foreach ($conversations as $conversation): ?>
				<?php $otherUser = $conversation->getOtherUser(); ?>
				<li class="conversation-item" data-conversation-id="<?php echo htmlspecialchars($conversation->getId()); ?>">
					<div class="conversation-avatar">
						<?php if ($otherUser && $otherUser['profile_picture']): ?>
							<img src="<?php echo htmlspecialchars($baseUrl . $otherUser['profile_picture']); ?>" alt="Avatar de <?php echo htmlspecialchars($otherUser['username']); ?>">
						<?php else: ?>
							<img src="<?php echo htmlspecialchars($baseUrl . '/assets/img/default-avatar.png'); ?>" alt="Avatar par défaut">
						<?php endif; ?>
					</div>
					<div class="conversation-info">
						<span class="conversation-name"><?php echo htmlspecialchars($otherUser['username'] ?? 'Utilisateur'); ?></span>
						<span class="conversation-last-message"><?php echo htmlspecialchars($conversation->getLastMessage() ?? 'Aucun message'); ?></span>
						<span class="conversation-time"><?php echo htmlspecialchars($conversation->getLastSentAt() ? date('H:i', strtotime($conversation->getLastSentAt())) : ''); ?></span>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ($totalPages > 1): ?>
			<div class="pagination">
				<?php if ($currentPage > 1): ?>
					<a href="<?php echo $baseUrl; ?>/messages?page=<?php echo $currentPage - 1; ?>" class="btn-prev">Précédent</a>
				<?php endif; ?>

				<span>Page <?php echo $currentPage; ?> sur <?php echo $totalPages; ?></span>

				<?php if ($currentPage < $totalPages): ?>
					<a href="<?php echo $baseUrl; ?>/messages?page=<?php echo $currentPage + 1; ?>" class="btn-next">Suivant</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="chat-area">
		<div id="chat-header" class="chat-header">
			<h2>Choisissez une conversation</h2>
		</div>
		<div id="chat-messages" class="chat-messages">
		</div>
		<form id="chat-form" class="chat-form" method="POST" action="" novalidate>
			<textarea id="chat-input" name="message" placeholder="Écrire un message..." disabled required></textarea>
			<button type="submit" class="btn-submit" disabled>Envoyer</button>
		</form>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const conversationItems = document.querySelectorAll('.conversation-item');
		const chatHeader = document.getElementById('chat-header');
		const chatMessages = document.getElementById('chat-messages');
		const chatForm = document.getElementById('chat-form');
		const chatInput = document.getElementById('chat-input');
		const chatSubmit = document.querySelector('#chat-form .btn-submit');

		let currentConversationId = null;

		conversationItems.forEach(item => {
			item.addEventListener('click', function() {
				conversationItems.forEach(i => i.classList.remove('active'));
				this.classList.add('active');

				const conversationId = this.getAttribute('data-conversation-id');
				currentConversationId = conversationId;

				chatInput.removeAttribute('disabled');
				chatSubmit.removeAttribute('disabled');

				const name = this.querySelector('.conversation-name').textContent;
				chatHeader.innerHTML = `<h2>Conversation avec ${name}</h2>`;

				loadMessages(conversationId);
			});
		});

		chatForm.addEventListener('submit', function(e) {
			e.preventDefault();
			if (!currentConversationId) {
				alert('Veuillez sélectionner une conversation.');
				return;
			}

			const message = chatInput.value.trim();
			if (message === '') {
				alert('Le message ne peut pas être vide.');
				return;
			}

			fetch(`<?php echo $baseUrl; ?>/messages/send_ajax`, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					conversation_id: currentConversationId,
					message: message,
				}),
			})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						chatInput.value = '';
						loadMessages(currentConversationId);
					} else {
						alert(data.error || 'Erreur lors de l\'envoi du message.');
					}
				})
				.catch(error => {
					console.error('Erreur:', error);
				});
		});

		function loadMessages(conversationId) {
			fetch(`<?php echo $baseUrl; ?>/messages/fetch_conversation?conversation_id=${conversationId}`)
				.then(response => response.json())
				.then(data => {
					if (data.messages) {
						chatMessages.innerHTML = '';
						data.messages.forEach(message => {
							const messageDiv = document.createElement('div');
							messageDiv.classList.add('message');
							messageDiv.classList.add(message.sender_id === <?php echo json_encode($_SESSION['user']['id']); ?> ? 'sent' : 'received');
							messageDiv.innerHTML = `<p>${escapeHtml(message.content)}</p><span>${message.sent_at}</span>`;
							chatMessages.appendChild(messageDiv);
						});
						chatMessages.scrollTop = chatMessages.scrollHeight;
					}
				})
				.catch(error => console.error('Erreur:', error));
		}

		function escapeHtml(text) {
			const map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;',
			};
			return text.replace(/[&<>"']/g, function(m) { return map[m]; });
		}

		setInterval(() => {
			if (currentConversationId) {
				loadMessages(currentConversationId);
			}
		}, 15000);
	});
</script>
