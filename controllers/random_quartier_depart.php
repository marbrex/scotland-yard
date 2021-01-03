<?php

// On recupere les informations sur les quartiers dont l'attribut isQuartierDepart vaut true
$req = "SELECT * FROM scotlandyard_project.quartier WHERE isQuartierDepart=1;";
$res = mysqli_query($connection, $req);

// Tableau qui va contenir tous les identifiants des quartiers (ou isQuartierDepart == 1)
$tabQ = array();
$nbQDeDepart = 0;

$i = 0;
// On remplit le tableau
while ($row = mysqli_fetch_array($res)) {
	$tabQ[$i] = $row['idQ'];
	++$i;
	++$nbQDeDepart;
}


// Tableau de positions de departs pour chaque joueur
$idQDeDepart = array();
// Nombre de joueurs, y inclus le joueur et le MX
$_SESSION['nbJoueursTotal'] = $_SESSION['nbDetectives'] + 2;
// On declare une variable de la session
$_SESSION['idQDeDepart'] = array();


// Fonction qui retourne l'heure actuelle en ms
function make_seed() {
	list($usec, $sec) = explode(' ', microtime());
	return $sec + $usec * 1000000;
}
// On met seed a l'heure en ms, pour generer les vraies valeurs aleatoires
mt_srand(make_seed());


// On remplit le tableau avec les identifiants des quartiers aleatoires (ou isQuartierDepart vaut true)
for ($i=0; $i<$_SESSION['nbJoueursTotal']; ++$i) {
	$randIndex = array_rand($tabQ);
	$idQDeDepart[$i] = $tabQ[$randIndex];

	if ($i != 0) {
		// On teste la valeur generee pour eviter la redondance
		for ($j=0; $j<$i; ++$j) {
			while ($idQDeDepart[$i] == $idQDeDepart[$j]) {
				$randIndex = array_rand($tabQ);
				$idQDeDepart[$i] = $tabQ[$randIndex];
			}
		}
	}

	// On affecte la valeur generee a la variable de la session
	$_SESSION['idQDeDepart'][$i] = $idQDeDepart[$i];
}

/*echo "Nombre de detectives: $_SESSION[nbDetectives]\n";
for ($i=0; $i<$_SESSION['nbJoueursTotal']; ++$i) {
	echo $_SESSION['idQDeDepart'][$i]."\n";
}*/

/*$problem = false;
for ($t=0; $t<1000; ++$t) {
	for ($i=0; $i<$_SESSION['nbJoueursTotal']; ++$i) {
		for ($j=$i+1; $j<$_SESSION['nbJoueursTotal']; ++$j) {
			if ($_SESSION['idQDeDepart'][$i] == $_SESSION['idQDeDepart'][$j]) {
				$problem = true;
			} else {
				$problem = false;
			}
		}
	}
}
if ($problem) {
	echo "PROBLEM!";
} else {
	echo "OKAY!";
}*/


// les tickets pour la strategie econome
if($_SESSION['strategie']=="econome"){
    for ($i=0; $i<$_SESSION['nbJoueursTotal']-1; ++$i) {
    	// 0 ca veut dire - joueur humain
    	// tout le reste c'est les detectives
        $_SESSION['nbTicketsTaxi'][$i] = 10;
        $_SESSION['nbTicketsBus'][$i] = 8;
        $_SESSION['nbTicketsMetro'][$i] = 4;
    }
}


$_SESSION['tourActuel'] = 1;

?>