<div class="messaging-container">
	<div class="sidebar">
		<h2>Mes Conversations</h2>
		<ul class="conversation-list">
			<?php foreach ($conversations as $conv): ?>
				<?php
				$isActive = $activeConversation && ($activeConversation->getId() === $conv->getId());
				$classActive = $isActive ? 'active' : '';
				$displayName = $conv->getOtherUser()['username'] ?? 'Utilisateur';
				?>
				<li class="conversation-item <?= $classActive; ?>">
					<a href="<?= $baseUrl ?>/messages?conversation_id=<?= $conv->getId() ?>">
						<span class="conversation-name"><?= htmlspecialchars($displayName) ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ($totalPages > 1): ?>
			<div class="pagination">
				<?php for ($p = 1; $p <= $totalPages; $p++): ?>
					<a href="<?= $baseUrl ?>/messages?page=<?= $p ?>"><?= $p ?></a>
				<?php endfor; ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="chat-area">
		<?php if ($activeConversation): ?>
			<?php
			$userOneId = $activeConversation->getUserOneId();
			$userTwoId = $activeConversation->getUserTwoId();
			?>
			<h2>Conversation #<?= $activeConversation->getId() ?></h2>
			<div class="chat-messages" id="chat-messages">
				<?php if (!empty($messages)): ?>
					<?php foreach ($messages as $msg): ?>
						<?php $classSent = ($msg->getSenderId() === $userId) ? 'sent' : 'received'; ?>
						<div class="message <?= $classSent ?>" data-msg-id="<?= $msg->getId() ?>">
							<p><?= htmlspecialchars($msg->getContent()) ?></p>
							<span>
                <?php if ($msg->getSenderId() === $userId): ?>
					<?php if ($userId === $userOneId && $msg->isReadByUserTwo()): ?>
						<strong>(Lu)</strong>
					<?php elseif ($userId === $userTwoId && $msg->isReadByUserOne()): ?>
						<strong>(Lu)</strong>
					<?php endif; ?>
				<?php endif; ?>
								<?= htmlspecialchars($msg->getSentAt()) ?>
              </span>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<div class="chat-form">
				<textarea id="messageInput" placeholder="Écrivez votre message..."></textarea>
				<button id="sendBtn" class="btn btn-primary">Envoyer</button>
			</div>
		<?php else: ?>
			<p>Sélectionnez une conversation à gauche pour chatter.</p>
		<?php endif; ?>
	</div>
</div>
<script src="<?= $baseUrl ?>/assets/js/messagingMain.js"></script>
<?php if ($activeConversation): ?>
	<script>
		initMessaging({
			conversationId: <?= $activeConversation->getId() ?>,
			userId: <?= $userId ?>,
			userOneId: <?= $activeConversation->getUserOneId() ?>,
			userTwoId: <?= $activeConversation->getUserTwoId() ?>,
			baseUrl: '<?= $baseUrl ?>'
		});
	</script>
<?php endif; ?>
