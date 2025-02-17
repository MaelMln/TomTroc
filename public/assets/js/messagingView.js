document.addEventListener('DOMContentLoaded', function() {
	const container = document.querySelector('.conversation-container');
	if (!container) return;

	const conversationId = container.getAttribute('data-conversation-id');
	const baseUrl = container.getAttribute('data-base-url');

	const messagesDiv = document.getElementById('messages');
	const messageForm = document.getElementById('messageForm');
	const messageInput = document.getElementById('messageInput');

	function escapeHtml(text) {
		const map = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			"'": '&#039;',
		};
		return text.replace(/[&<>"']/g, m => map[m]);
	}

	messageForm.addEventListener('submit', function(e) {
		e.preventDefault();
		const message = messageInput.value.trim();
		if (!message) {
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
				message: message,
			}),
		})
			.then(r => r.json())
			.then(data => {
				if (data.success) {
					const newMessageDiv = document.createElement('div');
					newMessageDiv.classList.add('message', 'sent');
					newMessageDiv.innerHTML = `<p>${escapeHtml(message)}</p><span>${(new Date()).toLocaleString()}</span>`;
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

	function fetchMessages() {
	}
	setInterval(fetchMessages, 15000);

	messagesDiv.scrollTop = messagesDiv.scrollHeight;
});
