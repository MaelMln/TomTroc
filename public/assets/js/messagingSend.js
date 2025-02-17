document.addEventListener('DOMContentLoaded', () => {
	const container = document.querySelector('.send-message-container');
	if (!container) return;

	const conversationId = container.getAttribute('data-conversation-id');
	const baseUrl = container.getAttribute('data-base-url');

	const sendForm = document.getElementById('sendMessageForm');
	if (!sendForm) return;

	sendForm.addEventListener('submit', function(e) {
		e.preventDefault();
		const message = document.getElementById('message');
		const messageContent = message.value.trim();

		if (messageContent === '') {
			alert('Le message ne peut pas être vide.');
			return;
		}

		fetch(`${baseUrl}/messages/send_ajax`, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				conversation_id: conversationId,
				message: messageContent,
			}),
		})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					alert('Message envoyé avec succès.');
					message.value = '';
				} else {
					alert(data.error || 'Erreur lors de l\'envoi du message.');
				}
			})
			.catch(error => {
				console.error('Erreur:', error);
				alert('Une erreur est survenue lors de l\'envoi du message.');
			});
	});
});
