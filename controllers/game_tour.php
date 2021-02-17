<?php

if (isset($_POST['isFetch']) && $_POST['isFetch'] == 'true') {
	header('Content-type: application/json');
}

function randomQuartierTourDetectives($i,$res)
{
    $tabRoutesDet[$i] = array();
	$n = 0;
	$routeVersMX = false;
	$idQVersMX = 0;
	$transportVersMX = '';
	// On remplit le tableau
	while ($row = mysqli_fetch_array($res)) {
		$tabRoutesDet[$i][$n][0] = $row['idQArrivee'];
		$tabRoutesDet[$i][$n][1] = $row['transport'];
		++$n;
		// On teste s'il y a une route directe vers le quartier ou se trouve le MX
		if ($row['idQArrivee'] == $_SESSION['idQNextMX'][0]) {
			$routeVersMX = true;
			$idQVersMX = $row['idQArrivee'];
			$transportVersMX = $row['transport'];
		}
	}

	for ($j=0; $j<$_SESSION['nbDetectives']; ++$j) {
		// S'il y a une route directe vers MX, on la choisit et on sort de la boucle
		if ($routeVersMX) {
			if ($idQVersMX != $_SESSION['idQPosActuel'][1] &&
				$idQVersMX != $_SESSION['idQNextDet'][$j][0]) {
				$randomRouteDet[0] = $idQVersMX;
				$randomRouteDet[1] = $transportVersMX;
				break;
			}
		}
		// S'il n'y a pas de route directe, on choisit une route aleatoirement
		if ($j != $i) {
			if ($_SESSION['tourActuel'] == 1) {
				// On teste si le quartier est occupe par quelqu'un d'autre
				do {
					// On choisit une route aleatoire
					$randIndex = array_rand($tabRoutesDet[$i]);
					$randomRouteDet = $tabRoutesDet[$i][$randIndex];
				} while ($randomRouteDet[0] == $_SESSION['idQDeDepart'][$j]);
			} else {
				do {
					$randIndex = array_rand($tabRoutesDet[$i]);
					$randomRouteDet = $tabRoutesDet[$i][$randIndex];
				} while ($randomRouteDet[0] == $_SESSION['idQNextDet'][$j][0] ||
						 $randomRouteDet[0] == $_SESSION['idQNextPlayer'][0]);
			}
		}
	}
    return $randomRouteDet;
}
function dijkstra($depart,$MX,$connection)
{
    $dist= array();
    $Q= array();
    $prec= array();
    for($i=1;$i<200;$i++)
    {
        $dist[$i]=1000;
        $Q[$i]=1000;
        $prec[$i]=NULL;
    }
    $dist[$depart]=0;
    $Q[$depart]=0;
    $prec[$depart]=0;
    
    while(is_null($prec[$MX]))
    {
        $u=array_search(min($Q),$Q);
        $Q[$u]=1000;
        
        if($dist[$u]==1000)
            return false;
        
        $req = "SELECT * FROM scotlandyard_project.route where scotlandyard_project.route.idQDepart=".$u.";";
        $graph = mysqli_query($connection, $req);
        
        while($ligne= mysqli_fetch_assoc($graph))
        {
            $alt=$dist[$u]+1;
            if($alt<$dist[$ligne['idQArrivee']])
            {
                $dist[$ligne['idQArrivee']]=$alt;
                $prec[$ligne['idQArrivee']]=$u;
                $Q[$ligne['idQArrivee']]=$dist[$ligne['idQArrivee']];
            }
        }
        
    }
    
    $array=array();
    $pos=$MX;
    while($prec[$pos]!=0)
    {
        array_unshift($array,$pos);
        $pos=$prec[$pos];
    }
    return $array;
    
    
}





if ($_SESSION['tourActuel'] >= 20) {
	$req = "UPDATE scotlandyard_project.partie SET aGagne=0 WHERE idP=$_SESSION[idPActuel];";
	$res = mysqli_query($connection, $req);
	// Si la requete est echouee alors on affiche un message d'erreur
	if ($res == false) {
		write_log("The following request has been failed : $req\n");
	}
	$req = "UPDATE scotlandyard_project.joueur SET scoreJ=scoreJ-1 WHERE nicknameJ='$_SESSION[nicknameJActuel]';";
	$res = mysqli_query($connection, $req);
	if ($res == false) {
		write_log("The following request has been failed : $req\n");
	}
	
	$_SESSION['gameEndedMessage'] = "20+ tours!";
	$_SESSION['gameEnded'] = true;
	$_SESSION['gameover'] = true;
} else {


// On recupere la route choisie par le joueur
if (isset($_POST['idQChoisi']) && isset($_POST['transportChoisi'])) {
	if (!empty($_POST['idQChoisi']) && !empty($_POST['transportChoisi'])) {
		$_SESSION['idQNextPlayer'][0] = $_POST['idQChoisi'];
		$_SESSION['idQNextPlayer'][1] = $_POST['transportChoisi'];
	} else {
		echo "Selectionnez un quartier de destination!\n";
	}
} else {
	if ($_SESSION['tourActuel'] == 1) {
		$_SESSION['idQNextPlayer'][0] = $_SESSION['idQDeDepart'][1];
		$_SESSION['idQNextPlayer'][1] = '';
	}
}


if (isset($_POST['mapCenterLat']) && isset($_POST['mapCenterLng'])) {
	$_SESSION['mapCenterLat'] = $_POST['mapCenterLat'];
	$_SESSION['mapCenterLng'] = $_POST['mapCenterLng'];
} else {
	$_SESSION['mapCenterLat'] = 45.763420;
	$_SESSION['mapCenterLng'] = 4.834277;
}





//======= Les positions actuelles de tous les joueurs (y inclus MX et le joueur)=======
if ($_SESSION['tourActuel'] == 1) {
	$_SESSION['idQPosActuel'] = array();
	for ($i=0; $i<$_SESSION['nbJoueursTotal']; ++$i) {
		$_SESSION['idQPosActuel'][$i] = $_SESSION['idQDeDepart'][$i];
	}
} else {
	$_SESSION['idQPosActuel'][0] = $_SESSION['idQNextMX'][0];
	$_SESSION['idQPosActuel'][1] = $_SESSION['idQNextPlayer'][0];
	for ($i=2; $i<$_SESSION['nbJoueursTotal']; ++$i) {
		$_SESSION['idQPosActuel'][$i] = $_SESSION['idQNextDet'][$i-2][0];
	}
}





//============ Le prochain quartier du MX (calcule aleatoirement) ==============
if ($_SESSION['tourActuel'] == 1) {
	$posMX = $_SESSION['idQDeDepart'][0];
} else {
	$posMX = $_SESSION['idQNextMX'][0];
}
$req = "SELECT * FROM scotlandyard_project.route WHERE idQDepart=$posMX;";
$res = mysqli_query($connection, $req);

$tabRoutesMX = array();
$n = 0;
// On remplit le tableau
while ($row = mysqli_fetch_array($res)) {
	$tabRoutesMX[$n][0] = $row['idQArrivee'];
	$tabRoutesMX[$n][1] = $row['transport'];
	++$n;
}

// Pour array_diff()
for ($i=0; $i<$n; ++$i) {
	$tabQArriveeMX[$i] = $tabRoutesMX[$i][0];
}
for ($i=0; $i<$_SESSION['nbDetectives']+1; ++$i) {
	if ($_SESSION['tourActuel'] == 1) {
		$positionsDeDetectives[$i] = $_SESSION['idQDeDepart'][$i+1];
	} else {
		$positionsDeDetectives[$i] = $_SESSION['idQPosActuel'][$i+1];
	}
}

// On teste si MX se deplace sur un quartier occupe par un detective
for ($j=1; $j<$_SESSION['nbJoueursTotal']; ++$j) {
	$arrayDiff = array_diff($tabQArriveeMX, $positionsDeDetectives);
	// On teste s'il y a des quartiers ou MX peut aller
	if ($arrayDiff == NULL) {
		//echo 'Partie terminee, detectives ont gagne';
		$req = "UPDATE scotlandyard_project.partie SET aGagne=1 WHERE idP=$_SESSION[idPActuel];";
		$res = mysqli_query($connection, $req);
		if ($res == false) {
			write_log("The following request has been failed : $req\n");
		}
		$_SESSION['gameEndedMessage'] = "Mister X est entouré!";

		$req = "UPDATE scotlandyard_project.joueur SET scoreJ=scoreJ+1 WHERE nicknameJ='$_SESSION[nicknameJActuel]';";
		$res = mysqli_query($connection, $req);
		if ($res == false) {
			write_log("The following request has been failed : $req\n");
		}

		$_SESSION['gameEnded'] = true;
		$_SESSION['victory'] = true;
	} else {
		if ($_SESSION['tourActuel'] == 1) {
			do {
				$randIndex = array_rand($tabRoutesMX);
				$randomRouteMX = $tabRoutesMX[$randIndex];
			} while ($randomRouteMX[0] == $_SESSION['idQDeDepart'][$j]);
		} else {
			do {
				$randIndex = array_rand($tabRoutesMX);
				$randomRouteMX = $tabRoutesMX[$randIndex];
			} while ($randomRouteMX[0] == $_SESSION['idQPosActuel'][$j] ||
					 $randomRouteMX[0] == $_SESSION['idQNextPlayer'][0]);
		}
	}
}

// On affecte la valeur generee a la variable de la session
$_SESSION['idQNextMX'] = $randomRouteMX;
//echo "Quartier d'arrivee : ".$_SESSION['idQNextMX'][0].", transport : ".$_SESSION['idQNextMX'][1];





//=========== Le prochain quartier pour les detectives (machines) ===========
for ($i=0; $i<$_SESSION['nbDetectives']; ++$i) {
	if ($_SESSION['tourActuel'] == 1) {
		$posDet[$i] = $_SESSION['idQDeDepart'][$i+2];
	} else {
		$posDet[$i] = $_SESSION['idQNextDet'][$i][0];
	}

    $skip=false;
    //START ECONOME DETECTIVES--------------------------------------------------------------------------
    if($_SESSION['strategie']=='econome'){
        $skip=true;
        $taxi=false;
        $bus=false;
        $metro=false;
        $req = "SELECT DISTINCT transport FROM scotlandyard_project.route WHERE idQDepart=$posDet[$i];";
        $res = mysqli_query($connection, $req);
        while ($row = mysqli_fetch_array($res)){
            if($row['transport']=='Taxi')
                $taxi=true;
            if($row['transport']=='Bus')
                $bus=true;
            if($row['transport']=='Métro/tramway')
                $metro=true;
        }
        $totTickets=0;
        $ticketsTaxi=0;
        $ticketsBus=0;
        $ticketsMetro=0;
        if($taxi){
            $totTickets+=$_SESSION['nbTicketsTaxi'][$i+1];
            $ticketsTaxi=$_SESSION['nbTicketsTaxi'][$i+1];
        }
        if($bus){
            $totTickets+=$_SESSION['nbTicketsBus'][$i+1];
            $ticketsBus=$_SESSION['nbTicketsBus'][$i+1];
        }
        if($metro){
            $totTickets+=$_SESSION['nbTicketsMetro'][$i+1];
            $ticketsMetro=$_SESSION['nbTicketsMetro'][$i+1];
        }
        if($totTickets==0)
        {
            $_SESSION['idQNextDet'][$i][0]=$posDet[$i];
            //what for the transport?
            $_SESSION['idQNextDet'][$i][1]="None";
        }
        else{
            $ticketRandom=rand(0,$totTickets-1);
            if($taxi && $ticketRandom<$ticketsTaxi){
                $_SESSION['nbTicketsTaxi'][$i+1]--;
                
                $req = "SELECT * FROM scotlandyard_project.route WHERE idQDepart=$posDet[$i] and transport='Taxi';";
                $res = mysqli_query($connection, $req);
                $randomRouteDet=randomQuartierTourDetectives($i,$res);
	            // On affecte la valeur generee a la variable de la session
	            $_SESSION['idQNextDet'][$i][0] = $randomRouteDet[0];
	            $_SESSION['idQNextDet'][$i][1] = $randomRouteDet[1];
            }else{
                if($bus && $ticketRandom<$ticketsTaxi+$ticketsBus){
                    $_SESSION['nbTicketsBus'][$i+1]--;
                    
                    $req = "SELECT * FROM scotlandyard_project.route WHERE idQDepart=$posDet[$i] and transport='Bus';";
                    $res = mysqli_query($connection, $req);
                    $randomRouteDet=randomQuartierTourDetectives($i,$res);
	                // On affecte la valeur generee a la variable de la session
	                $_SESSION['idQNextDet'][$i][0] = $randomRouteDet[0];
	                $_SESSION['idQNextDet'][$i][1] = $randomRouteDet[1];
                }else{
                    $_SESSION['nbTicketsMetro'][$i+1]--;
                    
                    $req = "SELECT * FROM scotlandyard_project.route WHERE idQDepart=$posDet[$i] and transport='Métro/tramway';";
                    $res = mysqli_query($connection, $req);
                    $randomRouteDet=randomQuartierTourDetectives($i,$res);
	                // On affecte la valeur generee a la variable de la session
	                $_SESSION['idQNextDet'][$i][0] = $randomRouteDet[0];
                    $_SESSION['idQNextDet'][$i][1] = $randomRouteDet[1];
                }
            }
        }
    }
    //STOP ECONOME DETECTIVES--------------------------------------------------------------------------
    
    //START PISTAGE DETECTIVES--------------------------------------------------------------------------
        if($_SESSION['strategie']=='pistage' && $_SESSION['tourActuel']>2){
            if(in_array($_SESSION['tourActuel'],array(3,8,13,18))){
                
                
                $futurePos = array();
                $futerePos=dijkstra($posDet[$i],$_SESSION['idQNextMX'][0],$connection);
                $_SESSION['futurePos'][$i]=$futerePos;
                //print_r($_SESSION['futurePos'][$i]);
                
                $req = "SELECT * FROM scotlandyard_project.route WHERE idQDepart=$posDet[$i] and idQArrivee=".array_shift($_SESSION['futurePos'][$i]).";";
		        $res = mysqli_query($connection, $req);
                $row = mysqli_fetch_array($res);
                 
                $_SESSION['idQNextDet'][$i][0] = $row['idQArrivee'];
		        $_SESSION['idQNextDet'][$i][1] = $row['transport'];
                                
                
                $skip=true;
            }
            else{
                if(isset($_SESSION['futurePos'][$i]) && $_SESSION['futurePos'][$i]!=NULL){
                    
                    //On teste s'il y a une route directe vers le quartier ou se trouve le MX
                    $req = "SELECT * FROM scotlandyard_project.route WHERE idQDepart=$posDet[$i] and idQArrivee=".$_SESSION['idQNextMX'][0].";";
		            $res = mysqli_query($connection, $req);
                    if(mysqli_num_rows($res)!=0){
                        $_SESSION['futurePos'][$i]=NULL;
                        $row = mysqli_fetch_array($res);
                        $_SESSION['idQNextDet'][$i][0] = $row['idQArrivee'];
		                $_SESSION['idQNextDet'][$i][1] = $row['transport'];
                    }
                    else{
                        //print_r($_SESSION['futurePos'][$i]);
                        $req = "SELECT * FROM scotlandyard_project.route WHERE idQDepart=$posDet[$i] and idQArrivee=". array_shift($_SESSION['futurePos'][$i]).";";
		                $res = mysqli_query($connection, $req);
                        $row = mysqli_fetch_array($res);
                        
                        $_SESSION['idQNextDet'][$i][0] = $row['idQArrivee'];
		                $_SESSION['idQNextDet'][$i][1] = $row['transport'];
                        
                    }
                    
                    
                    $skip=true;
                }
            }
        }
        //STOP PISTAGE DETECTIVES------------------------------------------------------------
        
    //STRATEGIE BASIQUE----------------------------------------------------------------
    if(!$skip){
	   $req = "SELECT * FROM scotlandyard_project.route WHERE idQDepart=$posDet[$i];";
	   $res = mysqli_query($connection, $req);
    
       $randomRouteDet=randomQuartierTourDetectives($i,$res);
	   // On affecte la valeur generee a la variable de la session
	   $_SESSION['idQNextDet'][$i][0] = $randomRouteDet[0];
	   $_SESSION['idQNextDet'][$i][1] = $randomRouteDet[1];
    }
}





//=========== Les quartiers ou le joueur peut se rendre (avec les routes) ==============
if ($_SESSION['tourActuel'] == 1) {
	$departPlayer = $_SESSION['idQDeDepart'][1];
} else {
	$departPlayer = $_SESSION['idQNextPlayer'][0];
}

//PISTAGE GAMER-------------------------------------
if($_SESSION['strategie']=='pistage' && $_SESSION['tourActuel']>2){
    if(in_array($_SESSION['tourActuel'],array(3,8,13,18))){
        //include 'dijkstra.php';
        
        $futurePos= array();
        $futerePos=dijkstra($departPlayer,$_SESSION['idQNextMX'][0],$connection);
        $_SESSION['futurePosPlayer']=$futerePos;
        //print_r($_SESSION['futurePosPlayer']);
    }
    /*else{
        if(isset($_SESSION['futurePosPlayer']) && $_SESSION['futurePosPlayer']!=NULL){
            $next=array_shift($_SESSION['futurePosPlayer']);
            //print_r($_SESSION['futurePosPlayer']);
        }
    }*/
}
//PISTAGE END-------------------------------------------------

$req = "SELECT * FROM scotlandyard_project.route WHERE idQDepart=$departPlayer;";
$res = mysqli_query($connection, $req);

$tabRoutesPlayer = array();
$nbRoutesPlayer = 0;

$i = 0;
// On remplit le tableau
while ($row = mysqli_fetch_array($res)) {
	$tabRoutesPlayer[$i][0] = $row['idQArrivee'];
	$tabRoutesPlayer[$i][1] = $row['transport'];
	++$i;
	++$nbRoutesPlayer;
}

$_SESSION['tabRoutesPlayer'] = array();
$_SESSION['nbRoutesPlayer'] = $nbRoutesPlayer;
for ($i=0; $i<$nbRoutesPlayer; ++$i) {
	$_SESSION['tabRoutesPlayer'][$i][0] = $tabRoutesPlayer[$i][0];
	$_SESSION['tabRoutesPlayer'][$i][1] = $tabRoutesPlayer[$i][1];
}





//=================== On recupere l'identifiant de la partie en cours =================
$req = "SELECT MAX(idP) AS idP FROM scotlandyard_project.partie;";
$res = mysqli_query($connection, $req);
$row = mysqli_fetch_array($res);
$_SESSION['idPartieActuelle'] = $row['idP'];





//====================== On insere un tuple dans la table tourMX =========================
$req = "INSERT INTO scotlandyard_project.tourMX VALUES ($_SESSION[idPartieActuelle], $_SESSION[tourActuel], ".$_SESSION['idQPosActuel'][0].", ".$_SESSION['idQNextMX'][0].", '".$_SESSION['idQNextMX'][1]."');";
$res = mysqli_query($connection, $req);




//=================================== Logs ==========================================
$file = 'logs/log_tour_history.txt';
if ($_SESSION['tourActuel'] == 1) {
	$ligne = "Partie $_SESSION[idPActuel]";
	file_put_contents($file, $ligne);
}

$ligne = "\nTour $_SESSION[tourActuel]\n";
file_put_contents($file, $ligne, FILE_APPEND);

$ligne = "0 : ".$_SESSION['idQPosActuel'][0]." -> ".$_SESSION['idQNextMX'][0]." - ".$_SESSION['idQNextMX'][1]."\n";
file_put_contents($file, $ligne, FILE_APPEND);

$ligne = "1 : ".$_SESSION['idQPosActuel'][1]." -> ".$_SESSION['idQNextPlayer'][0]." - ".$_SESSION['idQNextPlayer'][1]."\n";
file_put_contents($file, $ligne, FILE_APPEND);

for($i=2; $i<$_SESSION['nbJoueursTotal']; ++$i) {
	$ligne = "$i : ".$_SESSION['idQPosActuel'][$i]." -> ".$_SESSION['idQNextDet'][$i-2][0]." - ".$_SESSION['idQNextDet'][$i-2][1]."\n";
	file_put_contents($file, $ligne, FILE_APPEND);
}





// On teste si MX se trouve dans le meme quartier qu'un des detectives
if ($_SESSION['tourActuel'] != 1) {
	if ($_SESSION['idQNextMX'][0] == $_SESSION['idQNextPlayer'][0]) {
		// Le joueur humain a attrape MX
		$req = "UPDATE scotlandyard_project.partie SET aGagne=1 WHERE idP=$_SESSION[idPActuel];";
		$res = mysqli_query($connection, $req);
		if ($res == false) {
			write_log("The following request has been failed : $req\n");
		}
		$_SESSION['gameEndedMessage'] = "Vous avez attrapé Mister X!";

		$req = "UPDATE scotlandyard_project.joueur SET scoreJ=scoreJ+1 WHERE nicknameJ='$_SESSION[nicknameJActuel]';";
		$res = mysqli_query($connection, $req);
		if ($res == false) {
			write_log("The following request has been failed : $req\n");
		}

		$_SESSION['gameEnded'] = true;
		$_SESSION['victory'] = true;
	}
	for ($i=0; $i<$_SESSION['nbDetectives']; ++$i) {
		if ($_SESSION['idQNextMX'][0] == $_SESSION['idQNextDet'][$i][0]) {
			// Le detective $i a attrape MX
			$req = "UPDATE scotlandyard_project.partie SET aGagne=1 WHERE idP=$_SESSION[idPActuel];";
			$res = mysqli_query($connection, $req);
			if ($res == false) {
				write_log("The following request has been failed : $req\n");
			}
			$_SESSION['gameEndedMessage'] = "Le detective ".($i+1)." a attrapé Mister X!";

			$req = "UPDATE scotlandyard_project.joueur SET scoreJ=scoreJ+1 WHERE nicknameJ='$_SESSION[nicknameJActuel]';";
			$res = mysqli_query($connection, $req);
			if ($res == false) {
				write_log("The following request has been failed : $req\n");
			}

			$_SESSION['gameEnded'] = true;
			$_SESSION['victory'] = true;
		}
	}
}

}




// Passing PHP variables to JS (RETURNING them in a JSON format to the client)
if (isset($_POST['isFetch']) && $_POST['isFetch'] == 'true') {
	header('Content-type: application/json');
	echo json_encode($_SESSION);

	if (isset($_SESSION['gameEnded']) && $_SESSION['gameEnded'] == true) {
		session_destroy();
	}

	// Le Mister X se deplace
	$_SESSION['idQPosActuel'][0] = $_SESSION['idQNextMX'][0]; // idQ
	$_SESSION['transportUtilise'][0] = $_SESSION['idQNextMX'][1]; // transport

	// On incremente le tour actuel
	++$_SESSION['tourActuel'];
}

?>