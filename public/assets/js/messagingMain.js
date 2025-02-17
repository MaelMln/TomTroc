let conversationId = 0;
let userId = 0;
let userOneId = 0;
let userTwoId = 0;
let baseUrl = '';

function initMessaging(config) {
	conversationId = config.conversationId;
	userId = config.userId;
	userOneId = config.userOneId;
	userTwoId = config.userTwoId;
	baseUrl = config.baseUrl;

	const sendBtn = document.getElementById('sendBtn');
	const messageInput = document.getElementById('messageInput');

	if (sendBtn && messageInput) {
		sendBtn.addEventListener('click', function() {
			const content = messageInput.value.trim();
			if (content) {
				sendMessageAjax(content);
				messageInput.value = '';
			}
		});
	}

	setInterval(fetchConversation, 5000);
	scrollChatToBottom();
}

function sendMessageAjax(content) {
	fetch(`${baseUrl}/messages/send_ajax`, {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ conversation_id: conversationId, message: content })
	})
		.then(r => r.json())
		.then(data => {
			if (data.error) {
				alert('Erreur : ' + data.error);
			} else {
				fetchConversation();
			}
		})
		.catch(err => console.error('Erreur sendMessageAjax:', err));
}

function fetchConversation() {
	fetch(`${baseUrl}/messages/fetch_conversation?conversation_id=${conversationId}`)
		.then(r => r.json())
		.then(data => {
			if (data.messages) {
				renderConversation(data.messages);
			}
		})
		.catch(err => console.error('Erreur fetchConversation:', err));
}

function renderConversation(messages) {
	const chatBox = document.getElementById('chat-messages');
	if (!chatBox) return;

	chatBox.innerHTML = '';

	messages.forEach(msg => {
		const div = document.createElement('div');
		div.classList.add('message');
		div.classList.add(msg.sender_id == userId ? 'sent' : 'received');
		div.setAttribute('data-msg-id', msg.id);

		let readIndicator = '';
		if (msg.sender_id == userId) {
			if (userId == userOneId && msg.is_read_by_user_two) {
				readIndicator = ' <strong>(Lu)</strong>';
			} else if (userId == userTwoId && msg.is_read_by_user_one) {
				readIndicator = ' <strong>(Lu)</strong>';
			}
		}

		div.innerHTML = `
			<p>${msg.content}</p>
			<span>${msg.sent_at}${readIndicator}</span>
		`;

		chatBox.appendChild(div);
	});

	scrollChatToBottom();
}

function scrollChatToBottom() {
	const chatBox = document.getElementById('chat-messages');
	if (chatBox) {
		chatBox.scrollTop = chatBox.scrollHeight;
	}
}

window.initMessaging = initMessaging;
