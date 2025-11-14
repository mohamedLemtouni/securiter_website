<!DOCTYPE html>
<html lang="en">

<head>
	<title>Connexion / Inscription</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://unicons.iconscout.com/release/v2.1.9/css/unicons.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="/static/css/conex.css">
	<link rel="stylesheet" href="/static/css/header.css">
  	<link rel="stylesheet" href="/static/css/footer.css">
 	<title>Azure Travel</title>
    <link rel="icon" sizes="192x192" href="./photos/logo_wbl.png">
</head>

<?php include("header.php") ?>
<body>
	<div class="section">
		<div class="container">
			<div class="row full-height justify-content-center">
				<div class="col-12 text-center align-self-center py-5">
					<div class="section pb-5 pt-5 pt-sm-2 text-center">
						<h6 style="color: black ;" class="mb-0 pb-3"><span>Se connecter </span><span
								style="color: black ;">S'inscrire</span></h6>
						<input class="checkbox" type="checkbox" id="reg-log" name="reg-log" />
						<label for="reg-log"></label>
						<div class="card-3d-wrap mx-auto">

							<div style="height: 500px; width: 470px;" class="card-3d-wrapper">
								<div class="card-front" id="connexion">
									<div class="center-wrap">
										<div class="section text-center">
											<h4 class="mb-4 pb-3">Connexion</h4>
											<!-- Log In Form -->
											<form action="traitementconn.php?value=conn" method="post">
												<div class="form-group">
													<input type="email" id="email" class="form-style" name="email"
														placeholder="E-mail" required autofocus>
													<i class="input-icon uil uil-at"></i>
												</div>
												<div class="form-group mt-2">
													<input type="password" id="pdw" class="form-style" name="password"
														placeholder="Mot de passe" required>
													<i class="input-icon uil uil-lock-alt"></i>
												</div>
												<button type="submit" class="btn mt-4">Se connecter</button>
											</form>
											<!-- End of Log In Form -->
											<p class="mb-0 mt-4 text-center"><a href="motdepasseoublier.php"
													class="link">Mot de passe
													oublié ?</a></p>
										</div>
									</div>
								</div>
								<div class="card-back" id="inscription">
									<div class="center-wrap">
										<div class="section text-center">
											<h4 class="mb-3 pb-3">Inscription</h4>
											<!-- Sign Up Form -->
											<form action="traitementconn.php?value=inscription" method="post">
												<div class="form-group" style="display: flex;">
													
													<input type="text" id="nom" class="form-style" name="nom"
														style="margin-right: 5px;" placeholder="Nom" required>
													<i class="input-icon uil uil-user"></i>
													<input type="text" id="prenom" class="form-style" name="prenom"
														placeholder="Prénom" required>
												</div>
												<div class="form-group mt-2">
													<input type="tel" id="numtel" class="form-style" name="numtel"
														placeholder="+4176 235 22 21" required>
													<i class="input-icon uil uil-phone"></i>
												</div>
												<script>
													function formattageNumTel() {
														const input = document.getElementById('numtel');
														let numTel = input.value.replace(/\D/g, '');

														if (numTel.length === 11) {
															let numTelFormate = '+';
															numTelFormate += numTel.substring(0, 4) + ' ';
															numTelFormate += numTel.substring(4, 7) + ' ';
															numTelFormate += numTel.substring(7, 9) + ' ';
															numTelFormate += numTel.substring(9, 11);
															input.value = numTelFormate;
														}
														else if (numtel.length > 11) {
															alert('Le numéro de téléphone doit avoir 11 chiffres.');
															return false;

														}
													}

													const numtelInput = document.getElementById('numtel');
													numtelInput.addEventListener('input', formattageNumTel);
												</script>


												<div class="form-group mt-2">
													<input type="email" id="email" class="form-style" name="email"
														placeholder="E-mail" required>
													<i class="input-icon uil uil-at"></i>
												</div>
												<div class="form-group mt-2">
													<input type="password" id="password" class="form-style"
														name="password" placeholder="Mot de passe" minlength="9"
														pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&amp;])[A-Za-z\d@$!%*?&amp;]{8,20}$"
														title="Le mot de passe doit contenir au moins 8 caractères, avec au moins une lettre en Majuscule, un chiffre et un caractère spécial (@$!%*#?&amp;)"
														required>
													<i class="input-icon uil uil-lock-alt"></i>
												</div>
												<div class="form-group mt-2">
													<input style="background-color: white;" type="file" id="profilpic"
														class="form-style" name="profilpic"
														placeholder="photo de profil" accept="image/*">
													<i class="input-icon uil uil-image-plus"></i>
												</div>
												<button type="submit" class="btn mt-4">S'enregistrer</button>
											</form>
											<!-- End of Sign Up Form -->
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- your JavaScript script here -->
</body>

</html>

<script>

	document.addEventListener("DOMContentLoaded", function () {
		var numtelInput = document.getElementById("numtel");
		numtelInput.value = "+41";
	});
</script>

<script>
	document.addEventListener("DOMContentLoaded", function () {
		const checkbox = document.getElementById("reg-log");
		const connexionSection = document.getElementById("connexion");
		const inscriptionSection = document.getElementById("inscription");

		const params = new URLSearchParams(window.location.search);
		const isSignUpMode = params.get('mode') === 'insc';

		if (isSignUpMode) {
			checkbox.checked = true;
			connexionSection.classList.add("hidden");
			inscriptionSection.classList.remove("hidden");
		} else {
			checkbox.checked = false;
			connexionSection.classList.remove("hidden");
			inscriptionSection.classList.add("hidden");
		}
	});
</script>

</body>

<?php include("footer.php")?>

</html>