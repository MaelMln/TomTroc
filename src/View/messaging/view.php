<div class="conversation-container">
	<h1>Conversation avec <?php
		$otherUserId = ($conversation->getUserOneId() === $_SESSION['user']['id']) ? $conversation->getUserTwoId() : $conversation->getUserOneId();
		$userRepo = new \App\Repository\UserRepository();
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
		<?php foreach ($messages as $message): ?>
			<div class="message <?php echo ($message->getSenderId() === $_SESSION['user']['id']) ? 'sent' : 'received'; ?>">
				<p><?php echo nl2br(htmlspecialchars($message->getContent())); ?></p>
				<span><?php echo htmlspecialchars($message->getSentAt()); ?></span>
			</div>
		<?php endforeach; ?>
	</div>

	<form id="messageForm" method="POST" action="<?php echo $baseUrl; ?>/messages/view?conversation_id=<?php echo htmlspecialchars($conversation->getId()); ?>" novalidate>
		<div class="form-group">
			<textarea id="messageInput" name="message" placeholder="Écrire un message..." required></textarea>
		</div>
		<button type="submit" class="btn-submit">Envoyer</button>
	</form>
</div>

<script>
	const conversationId = <?php echo json_encode($conversation->getId()); ?>;
	let lastMessageId = <?php echo !empty($messages) ? end($messages)->getId() : 0; ?>;

	function fetchMessages() {
		fetch(`<?php echo $baseUrl; ?>/messages/fetch?conversation_id=${conversationId}&last_message_id=${lastMessageId}`)
			.then(response => response.json())
			.then(data => {
				if (data.messages && data.messages.length > 0) {
					const messagesDiv = document.getElementById('messages');
					data.messages.forEach(message => {
						const messageDiv = document.createElement('div');
						messageDiv.classList.add('message');
						messageDiv.classList.add(message.sender_id === <?php echo json_encode($_SESSION['user']['id']); ?> ? 'sent' : 'received');
						messageDiv.innerHTML = `<p>${escapeHtml(message.content)}</p><span>${message.sent_at}</span>`;
						messagesDiv.appendChild(messageDiv);
						lastMessageId = message.id;
					});
					messagesDiv.scrollTop = messagesDiv.scrollHeight;
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
			"'": '&#039;'
		};
		return text.replace(/[&<>"']/g, function(m) { return map[m]; });
	}

	setInterval(fetchMessages, 15000);

	document.addEventListener("DOMContentLoaded", function() {
		const messagesDiv = document.getElementById('messages');
		messagesDiv.scrollTop = messagesDiv.scrollHeight;
	});
</script>
