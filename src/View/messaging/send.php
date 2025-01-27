<div class="send-message-container">
	<h1>Envoyer un message</h1>

	<?php if (!empty($errors)): ?>
		<div class="errors">
			<ul>
				<?php foreach ($errors as $error): ?>
					<li><?php echo htmlspecialchars($error); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<form id="sendMessageForm" method="POST" action="" novalidate>
		<div class="form-group">
			<label for="message">Votre Message</label>
			<textarea id="message" name="message" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
		</div>
		<button type="submit" class="btn-submit">Envoyer</button>
	</form>
</div>

<script>
	document.getElementById('sendMessageForm').addEventListener('submit', function(e) {
		e.preventDefault();
		const messageContent = document.getElementById('message').value.trim();
		if (messageContent === '') {
			alert('Le message ne peut pas être vide.');
			return;
		}

		fetch('<?php echo $baseUrl; ?>/messages/send_ajax', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				conversation_id: <?php echo json_encode($conversation_id); ?>,
				message: messageContent,
			}),
		})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					alert('Message envoyé avec succès.');
					document.getElementById('message').value = '';
				} else {
					alert(data.error || 'Erreur lors de l\'envoi du message.');
				}
			})
			.catch(error => {
				console.error('Erreur:', error);
				alert('Une erreur est survenue lors de l\'envoi du message.');
			});
	});
</script>
