<?php

//=========================================================================
//====================== L'integration des donnees ========================
//=========================================================================

set_time_limit(500);

require_once('model/model.php');

$connectionSource = getConnectBD("localhost", "root", "", "scotlandyard_source");
$connection = getConnectBD("localhost", "root", "", "scotlandyard_project");


//======================== Commune ========================//
$req = "SELECT DISTINCT cpCommune, nomCommune, departement FROM scotlandyard_source.Quartiers;";
$result = mysqli_query($connectionSource, $req);
/*if ($result == false) {
	echo '$result == false'."\n";
} else if ($result == true) {
	echo '$result == true'."\n";
} else {
	echo '$result est un objet'."\n";
}*/
while ($ligne = mysqli_fetch_assoc($result)) {
    $req = "INSERT INTO scotlandyard_project.commune VALUES ($ligne[cpCommune], '$ligne[nomCommune]', $ligne[departement]);";
    mysqli_query($connection, $req);
	/*if ($result2 == false) {
		echo '$result (2) == false'."\n";
	} else if ($result2 == true) {
		echo '$result (2) == true'."\n";
	} else {
		echo '$result (2) est un objet'."\n";
	}*/
}


//======================== Quartier ========================//
$req = "SELECT DISTINCT idQ, nomQ, codeInsee, typeQ, coords, isQuartierDepart, cpCommune FROM scotlandyard_source.Quartiers q INNER JOIN scotlandyard_source.Routes r ON q.idQ=r.idQuartierDepart;";
$result = mysqli_query($connectionSource, $req);
/*if ($result == false) {
	echo '$result == false'."\n";
} else if ($result == true) {
	echo '$result == true'."\n";
} else {
	echo '$result est un objet'."\n";
}*/
while ($ligne = mysqli_fetch_assoc($result)) {
	//===================== Optimisation des noms ====================
	$nomQ = $ligne['nomQ'];

	// On verifie s'il y a des apostrophes dans les champs
	if (strpos($ligne['nomQ'], "'") !== false) {
		// Si c'est le cas on les elimine
	    $tabNomQ = explode("'", $ligne['nomQ']);
	    $nomQ = "$tabNomQ[0]''$tabNomQ[1]";
	}


	//================= Optimisation des coordonnees =================
	// Coordonnees des quartiers
	$coordsQ1 = $ligne['coords'];
	// On supprime les parentheses de deux cotes
	$coordsQ1 = substr($coordsQ1, 3, -3);
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


	//================= Insertion des tuples =================
	// L'insertion selon l'attribut "isQuartierDepart"
	if ($ligne['isQuartierDepart'] == 1) {
		// Les quartiers qui sont un point de depart
	    $req = "INSERT INTO scotlandyard_project.quartier VALUES ($ligne[idQ], '$nomQ', $ligne[codeInsee], '$ligne[typeQ]', '$coordsQ', 1, $ligne[cpCommune]);";
	} else {
		// Les quartiers qui ne sont pas un point de depart
		$req = "INSERT INTO scotlandyard_project.quartier VALUES ($ligne[idQ], '$nomQ', $ligne[codeInsee], '$ligne[typeQ]', '$coordsQ', 0, $ligne[cpCommune]);";
	}
	mysqli_query($connection, $req);
}


//======================== Route ========================
$req = "SELECT idQuartierDepart, idQuartierArrivee, transport FROM scotlandyard_source.Routes;";
$result = mysqli_query($connectionSource, $req);
/*if ($result == false) {
	echo '$result == false'."\n";
} else if ($result == true) {
	echo '$result == true'."\n";
} else {
	echo '$result est un objet'."\n";
}*/
while ($ligne = mysqli_fetch_assoc($result)) {
    $req = "INSERT INTO scotlandyard_project.route VALUES ($ligne[idQuartierDepart], $ligne[idQuartierArrivee], '$ligne[transport]');";
    mysqli_query($connection, $req);
}


//======================== Configuration ========================
$req = "INSERT INTO scotlandyard_project.configuration VALUES ('basique', '".date("Y-m-d")."', 'basique');";
$res = mysqli_query($connection, $req);

$req = "INSERT INTO scotlandyard_project.configuration VALUES ('econome', '".date("Y-m-d")."', 'econome');";
$res = mysqli_query($connection, $req);

$req = "INSERT INTO scotlandyard_project.configuration VALUES ('pistage', '".date("Y-m-d")."', 'pistage');";
$res = mysqli_query($connection, $req);

if ($res == false) {
	write_log("The following request has been failed : $req");
}


mysqli_close($connectionSource);
mysqli_close($connection);

?>
