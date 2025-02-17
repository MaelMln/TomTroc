document.addEventListener('DOMContentLoaded', function() {
	function updateUnreadCount() {
		fetch(`${baseUrl}/messages/count_unread`)
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
