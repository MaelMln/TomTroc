<div class="register-container">
	<h2>Inscription</h2>

	<?php if (isset($errors) && !empty($errors)): ?>
		<div class="errors">
			<ul>
				<?php foreach ($errors as $error): ?>
					<li><?php echo htmlspecialchars($error); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<form action="<?php echo $baseUrl; ?>/register" method="POST">
		<div class="form-group">
			<label for="username">Nom d'utilisateur :</label>
			<input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
		</div>
		<div class="form-group">
			<label for="email">Email :</label>
			<input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
		</div>
		<div class="form-group">
			<label for="password">Mot de passe :</label>
			<input type="password" id="password" name="password" required>
		</div>
		<button type="submit">S'inscrire</button>
	</form>
</div>