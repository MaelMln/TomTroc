<div class="login-container">
	<div class="login-form">
		<h1>Connexion</h1>
		<form method="POST" action="/login">
			<div class="form-group">
				<label for="email">Adresse email</label>
				<input type="email" id="email" name="email" required>
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
