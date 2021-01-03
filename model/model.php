<?php

function getConnectBD($serveur, $user, $mdp, $bd) {
	$connexion = mysqli_connect($serveur, $user, $mdp, $bd);
	mysqli_set_charset($connexion, 'utf8mb4');
	if (mysqli_connect_errno()) {
		printf("Échec de la connexion: %s\n", mysqli_connect_error());
		exit();
	}
	return $connexion;
}

?>