<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title> <?= $websiteName ?> </title>
	<link rel="stylesheet" href="style/style.css" />
	<script src="js/jquery-3.4.1.min.js"></script>
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css" />
	<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"></script>
</head>
<body>
	<?php include("static/header.php"); ?>

	<?php include("static/menu.php"); ?>

	<?php include("static/statistics.php"); ?>

	<main id="page-content">
		<section id="quartier-map-section">
			<div id="game-ended-window">
				<span id="game-ended-title"></span>
				<p id="game-ended-message"></p>
				<a href="index.php#rules" class="button">Home</a>
				<a href="#" class="button" id="game-ended-again-btn">Again</a>
			</div>
			<div id="mapid"><!-- Le bloc qui va contenir la carte Leaflet --></div>
		</section>
	</main>

	<script type="text/javascript" src="js/leaflet-script.js"></script>

	<?php include("static/footer.php"); ?>

</body>
</html>