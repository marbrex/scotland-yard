<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title> <?= $websiteName ?> </title>
	<link rel="stylesheet" href="style/style.css">
	<script src="js/jquery-3.4.1.min.js"></script>
	<script src="js/menu.js"></script>
	<style>
		table.top-players {
			display: table;
			opacity: 1;
		}
	</style>
</head>
<body>
	<?php include("static/header.php"); ?>

	<?php include("static/menu.php"); ?>

	<?php include("static/statistics.php"); ?>

	<main>
		<section id="play">
			<?php
			echo '<table class="quartiers-et-routes">';

			echo '<thead>';
			echo '<tr> <th>ID</th> <th>Nom</th> <th>Commune</th> <th>Ou on peut se rendre</th> <th>Transport</th> </tr>';
			echo '</thead>';

			echo '<tbody>';
			$nbLignesQuartier = 0;
			$nbLignesRoute = 0;
			while ($instResultReqQuartier = mysqli_fetch_array($resultReqQuartier)) {
				echo '<tr>';
					$req = "SELECT COUNT(*) AS count FROM (SELECT * FROM (SELECT idQ, nomQ, nomC FROM project.quartier q NATURAL JOIN project.commune c) q INNER JOIN project.route r ON q.idQ=r.idQDepart) a WHERE idQ=".$instResultReqQuartier['idQ'].";";
					$resultNbIdQ = mysqli_query($connection, $req);
					$instResultNbIdQ = mysqli_fetch_array($resultNbIdQ);

					echo '<td rowspan="'.$instResultNbIdQ['count'].'" valign="center" class="quartiers">'.$instResultReqQuartier['idQ'].'</td>';
					echo '<td rowspan="'.$instResultNbIdQ['count'].'" valign="center" class="quartiers">'.$instResultReqQuartier['nomQ'].'</td>';
					echo '<td rowspan="'.$instResultNbIdQ['count'].'" valign="center" class="quartiers">'.$instResultReqQuartier['nomC'].'</td>';

					$nbLignesRoute2 = 0;
					for($i=0; $i < $instResultNbIdQ['count']; $i++) {
						$nbLignesRoute++;
						$nbLignesRoute2++;

						echo '<td class="routes">'.$instResultReqQuartier['idQArrivee'].'</td>';	
						echo '<td class="routes">'.$instResultReqQuartier['transport'].'</td>';

						echo '</tr>';

						if ($i == $instResultNbIdQ['count']-1) {
							if ($nbLignesRoute2 % 2 == 0) {
								echo '<tr></tr>';
							}
							break;
						}

						echo '<tr>';
						$instResultReqQuartier = mysqli_fetch_array($resultReqQuartier);
					}

				$nbLignesQuartier++;
				echo '</tr>';
			}
			echo '</tbody>';

			echo '</table>';

			echo "Nombre de quartiers : $nbLignesQuartier ";
			echo "Nombre de routes : $nbLignesRoute ";
			?>
		</section>
	</main>

	<?php include("static/footer.php"); ?>

</body>
</html>