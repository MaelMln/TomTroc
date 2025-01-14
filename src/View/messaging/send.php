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

	<form method="POST" action="<?php echo $baseUrl; ?>/messages/send?to=<?php echo htmlspecialchars($toUserId); ?>&book=<?php echo htmlspecialchars($bookId); ?>" novalidate>
		<div class="form-group">
			<label for="message">Votre Message</label>
			<textarea id="message" name="message" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
		</div>
		<button type="submit" class="btn-submit">Envoyer</button>
	</form>
</div>
