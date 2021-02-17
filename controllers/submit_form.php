<?php

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

$nicknameJ = $nameJ = $emailJ = '';
$errorNicknameJ = $errorNameJ = $errorEmailJ = '';

// Si le formulaire a ete soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Si le champs Nickname est vide, alors ...
	if (empty($_POST['playerNickname'])) {
		// on ecrit un message d'erreur
		$errorNicknameJ = 'Ce champs est obligatoire';
	} else {
		// sinon, on teste le contenu
		$nicknameJ = test_input($_POST['playerNickname']);
		// on ecrit un message d'erreur si le format n'est pas valide
		if (!preg_match("/^[a-z\d_]{3,20}$/i", $nicknameJ)) {
			$errorNicknameJ = 'Le nickname est incorrect';
		}
	}

	// Si le champs Prenom n'est pas vide, alors ...
	if (!empty($_POST['playerName'])) {
		// sinon, on teste le contenu
		$nameJ = test_input($_POST['playerName']);
		// on ecrit un message d'erreur si le format n'est pas valide (par exemple, s'il y a des nombres ou des caracteres speciaux) 
		if (!preg_match("/^[a-zA-Z ]*$/", $nameJ)) {
			$errorNameJ = 'Le prenom est incorrect';
		}
	}

	// Si le champs Mail n'est pas vide, alors ...
	if (!empty($_POST['playerMail'])) {
		// on supprime les espaces et les backslashes
		$emailJ = test_input($_POST['playerMail']);
		// on filtre le contenu, si le format du mail n'est pas valide, alors on ecrit un message d'erreur
		if (!filter_var($emailJ, FILTER_VALIDATE_EMAIL)) {
			$errorEmailJ = 'Le mail est incorrect';
		}
	}

	$nbDetectives = $_POST['nbDetectives'];
	$_SESSION['nbDetectives'] = $nbDetectives;

	$strategie = $_POST['strategie'];
    $_SESSION['strategie'] = $strategie;

	// Il n'y a pas des erreur
	if ($errorNicknameJ == '' && $errorEmailJ == '' && $errorNameJ == '') {
		// Si le prenom et le mail sont soumis
		if (isset($_POST['playerNickname']) && isset($_POST['playerMail'])) {
			// On teste le contenu des variables entrees par l'utilisateur(trice)
			$nicknameJ = mysqli_real_escape_string($connection, $nicknameJ);
			$emailJ = mysqli_real_escape_string($connection, $emailJ);

			// On teste si l'utilisateur(trice) est deja dans la base de donnee
			$req = "SELECT nicknameJ, emailJ FROM scotlandyard_project.joueur WHERE nicknameJ='$nicknameJ' AND emailJ='$emailJ';";
			$res = mysqli_query($connection, $req);

			// Si l'utilisateur(trice) n'est pas dans la base (cad la requete nous retourne le resultat vide), alors on l'ajoute dans la base
			if (mysqli_num_rows($res) == 0) {
				if (isset($_POST['playerName'])) {
					$nameJ = mysqli_real_escape_string($connection, $nameJ);
					$req = "INSERT INTO scotlandyard_project.joueur (nicknameJ, nameJ, emailJ) VALUES ('$nicknameJ', '$nameJ', '$emailJ');";
				} else {
					$req = "INSERT INTO scotlandyard_project.joueur (nicknameJ, emailJ) VALUES ('$nicknameJ', '$emailJ');";
				}
				$res = mysqli_query($connection, $req);
				// Si la requete est echouee alors on affiche un message d'erreur
				if ($res == false) {
					write_log("The following request has been failed : $req\n");
				}
			}

			// On recupere le prenom et le mail du joueur
			$req = "SELECT nameJ, emailJ FROM scotlandyard_project.joueur WHERE nicknameJ='$nicknameJ';";
			$res = mysqli_query($connection, $req);
			$row = mysqli_fetch_array($res);

			// On met a jour le prenom du joueur si besoin
			if (empty($row['nameJ'])) {
				$req = "UPDATE scotlandyard_project.joueur SET nameJ='$nameJ' WHERE nicknameJ='$nicknameJ'";
				$res = mysqli_query($connection, $req);
				// Si la requete est echouee alors on affiche un message d'erreur
				if ($res == false) {
					write_log("The following request has been failed : $req\n");
				}
			}

			// On met a jour le mail du joueur si besoin
			if (empty($row['emailJ'])) {
				$req = "UPDATE scotlandyard_project.joueur SET emailJ='$emailJ' WHERE nicknameJ='$nicknameJ'";
				$res = mysqli_query($connection, $req);
				// Si la requete est echouee alors on affiche un message d'erreur
				if ($res == false) {
					write_log("The following request has been failed : $req\n");
				}
			}

			// Peupler Partie
			$req = "INSERT INTO scotlandyard_project.partie (dateDebut, nbDetectives, nomConfig, nicknameJ) VALUES ('".date('Y-m-d')."', $nbDetectives, '$strategie', '$nicknameJ');";
			$res = mysqli_query($connection, $req);
			if ($res == false) {
				write_log("The following request has been failed : $req\n");
			}
		}

		// Si seul le prenom est soumis
		if (isset($_POST['playerNickname']) && !isset($_POST['playerMail'])) {
			$nicknameJ = mysqli_real_escape_string($connection, $nicknameJ);

			$req = "SELECT nicknameJ FROM scotlandyard_project.joueur WHERE nicknameJ='$nicknameJ';";
			$res = mysqli_query($connection, $req);

			if (mysqli_num_rows($res) == 0) {
				if (isset($_POST['playerName'])) {
					$nameJ = mysqli_real_escape_string($connection, $nameJ);
					$req = "INSERT INTO scotlandyard_project.joueur (nicknameJ, nameJ) VALUES ('$nicknameJ', '$nameJ');";
				} else {
					$req = "INSERT INTO scotlandyard_project.joueur (nicknameJ) VALUES ('$nicknameJ');";
				}
				$res = mysqli_query($connection, $req);
				if ($res == false) {
					write_log("The following request has been failed : $req\n");
				}
			}

			// Peupler Partie
			$req = "INSERT INTO scotlandyard_project.partie (dateDebut, nbDetectives, nomConfig, nicknameJ) VALUES ('".date('Y-m-d')."', $nbDetectives, '$strategie', '$nicknameJ');";
			$res = mysqli_query($connection, $req);
			if ($res == false) {
				write_log("The following request has been failed : $req\n");
			}
		}

		$req = "SELECT MAX(idP) AS idP FROM scotlandyard_project.partie;";
		$res = mysqli_query($connection, $req);
		$row = mysqli_fetch_array($res);
		$_SESSION['idPActuel'] = $row['idP'];
		$_SESSION['nicknameJActuel'] = $nicknameJ;



		require('controllers/random_quartier_depart.php'); // Script PHP qui genere les positions de depart pour chaque joueur
		if (!isset($_COOKIE['GeoJsonLoaded'])) {
			require('controllers/quartierData.php'); // Script PHP qui genere le fichier GeoJSON
			setcookie('GeoJsonLoaded', true, time() + (86400 * 7), "/"); // 86400 = 1 day
		}



		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
	         $url = "https://";
	    else
	         $url = "http://"; 
	    $url .= $_SERVER['HTTP_HOST'];

	    // On passe a la page de jeu (/views/quartier-map.php)
		header("Location: $url/scotlandyard/index.php?page=show-quartier-map");
	}
}

?>