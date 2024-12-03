<div class="register-container">
	<div class="register-form">
		<h1>Inscription</h1>

		<?php if (isset($errors) && !empty($errors)): ?>
			<div class="errors">
				<ul>
					<?php foreach ($errors as $error): ?>
						<li><?php echo htmlspecialchars($error); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<form method="POST" action="/register">
			<div class="form-group">
				<label for="username">Pseudo</label>
				<input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
			</div>
			<div class="form-group">
				<label for="email">Adresse email</label>
				<input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
			</div>
			<div class="form-group">
				<label for="password">Mot de passe</label>
				<input type="password" id="password" name="password" required>
			</div>
			<button type="submit" class="btn-submit">S'inscrire</button>
		</form>
		<p>Déjà inscrit ? <a href="/login">Connectez-vous</a></p>
	</div>
	<div class="register-image"></div>
</div>