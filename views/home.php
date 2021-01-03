<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title> <?= $websiteName ?> </title>
	<link rel="stylesheet" href="style/style.css">
	<script src="js/jquery-3.4.1.min.js"></script>
	<script src="js/header.js"></script>
	<script src="js/menu.js"></script>
	<script src="js/statistics.js"></script>
	<script src="js/statistics_click.js"></script>
</head>
<body>
	<?php include("static/header.php"); ?>

	<?php include("static/menu.php"); ?>

	<?php include("static/statistics.php"); ?>

	<main>
		<!--<section id="landing">
			<table class="player-games">
				<tr>
					<th>Total</th>
					<th>Victory</th>
					<th>Defeat</th>
				</tr>
				<tr>
					<td>12</td>
					<td>8</td>
					<td>4</td>
				</tr>
			</table>
			<a href="#rules" id="arrow-begin"><img src="images/arrow2.svg" alt="begin"></a>
		</section>-->

		<section id="rules">
			<h1>Bienvenue</h1>
			<p>Détectives essaient d’attraper Mister X. Les protagonistes se déplacent sur les 199 stations du plateau de jeu en utilisant taxi, métro, bus ou bateau. La position de Mister X est secrète, mais les détectives connaissent les moyens de transport qu’il utilise à chaque tour. De plus, Mister X doit dévoiler sa position à certains tours.</p>
		</section>

		<section id="about">
			<h1>About</h1>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nihil, temporibus mollitia non beatae corporis, aspernatur facere qui aut sint quas blanditiis enim fugit ea, culpa. Eveniet commodi, molestias odio et.</p>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Beatae nesciunt maiores sequi assumenda nobis enim asperiores veniam animi id quis. Voluptatibus, enim sequi, nulla in dolorum repudiandae? Mollitia consectetur perferendis voluptatem quidem, maxime molestias eaque minima commodi, unde maiores neque velit. Maiores dignissimos vel excepturi ipsam sit tempore atque libero. Velit libero fugiat iusto ipsam maxime quam excepturi, non atque.</p>
		</section>

		<section id="contact">
			<h1>Contact</h1>
		</section>
	</main>

	<?php include("static/footer.php"); ?>

</body>
</html>