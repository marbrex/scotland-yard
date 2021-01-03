<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title> <?= $websiteName ?> </title>
	<link rel="stylesheet" href="style/style.css">
	<script src="js/jquery-3.4.1.min.js"></script>
	<script src="js/statistics_click.js"></script>
	<style>
		table.top-players {
			display: table;
			opacity: 1;
		}
		@media screen and (max-width: 600px) {
			table.top-players {
				display: none;
			}
		}
	</style>
</head>
<body>
	<?php include("static/header.php"); ?>

	<?php include("static/menu.php"); ?>

	<?php include("static/statistics.php"); ?>

	<main>
		<section id="begin-to-play">
			<form action="#" method="POST">
				<input type="hidden" name="page" value="show-quartier-map">

				<input type="text" name="playerNickname" placeholder="Entrez votre nickname *" value="<?= $nicknameJ ?>" autofocus><br>
				<span class="form-validation-error"><?= $errorNicknameJ ?></span><br>

				<input type="text" name="playerName" placeholder="Entrez votre prenom" value="<?= $nameJ ?>"><br>
				<span class="form-validation-error"><?= $errorNameJ ?></span><br>

				<input type="text" name="playerMail" placeholder="Entrez votre email" value="<?= $emailJ ?>"><br>
				<span class="form-validation-error"><?= $errorEmailJ ?></span><br>

				<div class="nbDetectives">
					<span>Nombre de Detectives: *</span><br>
					<div>
						<label for="nbDetectives3">3</label>
						<input type="radio" name="nbDetectives" value="3" id="nbDetectives3" required>
					</div>
					<div>
						<label for="nbDetectives4">4</label>
						<input type="radio" name="nbDetectives" value="4" id="nbDetectives4" checked required>
					</div>
					<div>
						<label for="nbDetectives5">5</label>
						<input type="radio" name="nbDetectives" value="5" id="nbDetectives5" required>
					</div>
				</div>

				<div class="strategie">
                    <span>Strategie: *</span>
	                <select name="strategie" required>
						<option value="basique" id="strategieBasique">Basique</option>
						<option value="econome" id="strategieEconome">Econome</option>
						<option value="pistage" id="strategiePistage">Pistage</option>
					</select>
                </div><br>

				<span>* Obligatoire</span><br>

				<input type="submit" value="Commencer">
			</form>
		</section>
	</main>

	<?php include("static/footer.php"); ?>

</body>
</html>