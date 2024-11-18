<div class="user-list">
	<h2>Liste des utilisateurs</h2>
	<ul>
		<?php foreach ($users as $user): ?>
			<li>
				<?php echo htmlspecialchars($user->getUsername()); ?> - <?php echo htmlspecialchars($user->getEmail()); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
