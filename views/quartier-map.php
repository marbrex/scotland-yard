<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title> <?= $websiteName ?> </title>
	<link rel="stylesheet" href="style/style.css">
	<script src="js/jquery-3.4.1.min.js"></script>
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css" />
	<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"></script>
</head>
<body>
	<?php include("static/header.php"); ?>

	<?php include("static/menu.php"); ?>

	<?php include("static/statistics.php"); ?>

	<main>
		<section id="quartier-map-section">
			<div id="mapid"><!-- Le bloc qui va contenir la carte Leaflet -->
				<!-- <form action="index.php?page=show-quartier-map" method="POST" id="routeJoueur">
					<input type="hidden" name="idQChoisi" id="idQChoisi" value="">
					<input type="hidden" name="transportChoisi" id="transportChoisi" value="">
					<input type="hidden" name="joueurAChoisi" id="joueurAChoisi" value="">
					<input type="submit" value="Terminer" id="terminer-le-tour">
				</form> -->
			</div>
		</section>

		<div id="page-content">
			<script type="text/javascript" src="js/quartierData.js"></script>

			<?php require("controllers/leaflet_map_js_variables.php"); ?>
			<script type="text/javascript" src="js/leaflet-script.js"></script>

			<?php
				// Le Mister X se deplace
				$_SESSION['idQPosActuel'][0] = $_SESSION['idQNextMX'][0]; // idQ
				$_SESSION['transportUtilise'][0] = $_SESSION['idQNextMX'][1]; // transport

				// On incremente le tour actuel
				++$_SESSION['tourActuel'];
			?>
		</div>
		
	</main>

	<?php include("static/footer.php"); ?>

</body>
</html>