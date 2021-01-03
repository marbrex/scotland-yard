<?php

require_once('inc/includes.php');
require_once('model/model.php');
$connection = getConnectBD($server, $login, $pwd, $tableDB);

$req = "SELECT idQ, nomQ, coordsQ, nomC FROM scotlandyard_project.quartier q NATURAL JOIN scotlandyard_project.commune c ORDER BY idQ;";
$result = mysqli_query($connection, $req);


file_put_contents('js/quartierData.js', 'var quartierData = {
	"type" : "FeatureCollection",
	"features" : [');

while ($row = mysqli_fetch_array($result)) {
	//================= Optimisation des coordonnees =================
	// Coordonnees des quartiers
	$coordsQ1 = $row['coordsQ'];
	// On supprime les parentheses de deux cotes
	$coordsQ1 = substr($coordsQ1, 1, -1);
	// On partage les coordonnees des sommets d'un polygon
	$tabCoordsQ = explode('], [', $coordsQ1);
	$coordsQ = '';
	foreach ($tabCoordsQ as $key) {
		// On divise les coordonnees en deux (longitude et latitude), car elles sont pas dans le bon ordre
		$tabKey = explode(', ', $key);
		// On change l'ordre des coordonnees
		$coordsQ .= '['.$tabKey[1].', '.$tabKey[0].'], ';
	}
	// On enleve la derniere virgule et l'espace inseres
	$coordsQ = substr($coordsQ, 0, -2);

	file_put_contents('js/quartierData.js', '
	{
		"type" : "Feature",
		"id" : "'.$row['idQ'].'",
		"properties" : {
			"name" : "'.$row['nomQ'].'",
			"commune" : "'.$row['nomC'].'"
		},
		"geometry" : {
			"type" : "Polygon",
			"coordinates" : [
				['.$coordsQ.']
			]
		}
	},', FILE_APPEND);
	}

	file_put_contents('js/quartierData.js', '
	]
};
', FILE_APPEND);

?>