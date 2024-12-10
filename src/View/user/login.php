<div class="login-container">
	<div class="login-form">
		<h1>Connexion</h1>

		<?php if (isset($errors) && !empty($errors)): ?>
			<div class="errors">
				<ul>
					<?php foreach ($errors as $error): ?>
						<li><?php echo htmlspecialchars($error); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<form method="POST" action="/login" novalidate>
			<div class="form-group">
				<label for="email">Adresse email</label>
				<input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
			</div>
			<div class="form-group">
				<label for="password">Mot de passe</label>
				<input type="password" id="password" name="password" required>
			</div>
			<button type="submit" class="btn-submit">Se connecter</button>
		</form>
		<p>Pas de compte ? <a href="/register">Inscrivez-vous</a></p>
	</div>
	<div class="login-image"></div>
</div>
