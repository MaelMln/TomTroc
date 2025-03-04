<div class="conversation-container">
	<h1>Conversation avec
		<?php
		$otherUserId = ($conversation->getUserOneId() === $_SESSION['user']['id'])
			? $conversation->getUserTwoId()
			: $conversation->getUserOneId();
		$userRepo = new \App\Repository\UserRepository();
		$otherUser = $userRepo->findById($otherUserId);
		echo htmlspecialchars($otherUser->getUsername());
		?>
	</h1>
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
		<button type="submit" class="btn btn-primary">Envoyer</button>
	</form>
</div>
<script src="<?= $baseUrl ?>/assets/js/messagingView.js"></script>
