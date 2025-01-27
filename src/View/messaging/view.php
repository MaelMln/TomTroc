<div class="conversation-container">
	<h1>Conversation avec <?php
		$otherUserId = ($conversation->getUserOneId() === $_SESSION['user']['id'])
			? $conversation->getUserTwoId()
			: $conversation->getUserOneId();		$userRepo = new \App\Repository\UserRepository();
		$otherUser = $userRepo->findById($otherUserId);
		echo htmlspecialchars($otherUser->getUsername());
		?></h1>

	<?php if (!empty($errors)): ?>
		<div class="errors">
			<ul>
				<?php foreach ($errors as $error): ?>
					<li><?php echo htmlspecialchars($error); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<div id="messages" class="messages">
		<?php foreach ($messages as $message):
			$cssClass = ($message->getSenderId() === $_SESSION['user']['id']) ? 'sent' : 'received';
			?>
			<div class="message <?php echo $cssClass; ?>">
				<p><?php echo nl2br(htmlspecialchars($message->getContent())); ?></p>
				<span><?php echo $message->getSentAt(); ?></span>

				<?php
				if ($message->getSenderId() === $_SESSION['user']['id']) {
					if ($conversation->getUserOneId() === $_SESSION['user']['id']) {
						if ($message->isReadByUserTwo()) {
							echo '<span class="read-indicator">Lu</span>';
						}
					} else {
						if ($message->isReadByUserOne()) {
							echo '<span class="read-indicator">Lu</span>';
						}
					}
				}
				?>
			</div>
		<?php endforeach; ?>
	</div>

	<form id="messageForm" method="POST" action="<?php echo $baseUrl; ?>/messages/send_ajax" novalidate>
		<div class="form-group">
			<textarea id="messageInput" name="message" placeholder="Écrire un message..." required></textarea>
		</div>
		<button type="submit" class="btn-submit">Envoyer</button>
	</form>
</div>


<script>
	const isUserOne = <?php echo json_encode($isUserOne); ?>;
	const currentUserId = <?php echo json_encode($_SESSION['user']['id']); ?>;
	document.getElementById('messageForm').addEventListener('submit', function(e) {
		e.preventDefault();

		const messageInput = document.getElementById('messageInput');
		const message = messageInput.value.trim();
		const conversationId = <?php echo json_encode($conversation->getId()); ?>;

		if (message === '') {
			alert('Le message ne peut pas être vide.');
			return;
		}

		fetch('<?php echo $baseUrl; ?>/messages/send_ajax', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				conversation_id: conversationId,
				message: message,
			}),
		})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					const messagesDiv = document.getElementById('messages');
					const newMessageDiv = document.createElement('div');
					newMessageDiv.classList.add('message', 'sent');
					newMessageDiv.innerHTML = `<p>${escapeHtml(message)}</p><span><?php echo date('Y-m-d H:i:s'); ?></span>`;
					messagesDiv.appendChild(newMessageDiv);
					messagesDiv.scrollTop = messagesDiv.scrollHeight;
					messageInput.value = '';
				} else {
					alert(data.error || 'Erreur lors de l\'envoi du message.');
				}
			})
			.catch(error => {
				console.error('Erreur:', error);
				alert('Une erreur est survenue lors de l\'envoi du message.');
			});
	});

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

	setInterval(fetchMessages, 15000);

	function fetchMessages() {
	}

	document.addEventListener("DOMContentLoaded", function() {
		const messagesDiv = document.getElementById('messages');
		messagesDiv.scrollTop = messagesDiv.scrollHeight;
		if (typeof window.updateUnreadCount === 'function') {
			window.updateUnreadCount();
	});
</script>
