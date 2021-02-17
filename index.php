<?php

/*

Scotland Yard
Projet de:

Kasmamytov Eldar p1712650
 - Base de Donnees (la diagramme E/A, le schéma Relationnel)
 - HTML/CSS (Tout le code html et css)
 - Responsive Design (le design adaptive, il adapte a la largeur de l'ecran: les tablettes et les portables)
 - JavaScript/JQuery (Tout le code JS, Leaflet et GeoJson)
 - SQL et L'integration des donnees (J'ai utilise les requetes SQL pour recuperer les donnees de dataset et les boucles pour peupler ma base de donnee)
 - PHP (Toute la strategie basique, la generation des positions de depart, le deplacement des detectives et MX, etc.)

 !!! ATTENTION !!!
 C'est l'archive du projet que j'ai utilise en local.
 Tout le code est le meme, sauf que sur le serveur
 j'ai commente les lignes qui contiennent la fonction php
 file_put_contents().
 Elle ne marche pas a cause d'un probleme de droits
 sur le serveur. Donc, il n'y a pas des logs sur le serveur.

*/


session_start();
require('inc/includes.php');
require('inc/routes.php');
require('model/model.php');
if (!isset($_COOKIE['dataLoaded'])) {
	require('dataset.php'); // Script PHP qui peuple les tables de la BD locale
	setcookie('dataLoaded', true, time() + (86400 * 7), "/"); // 86400 = 1 day
}
$connection = getConnectBD($server, $login, $pwd, $tableDB);

$controller = '';
$view = 'home';

if(isset($_GET['page'])) {
	$pageName = $_GET['page'];
	
	if(isset($routes[$pageName])) {
		$controller = $routes[$pageName]['controller'];
		$view = $routes[$pageName]['view'];
	}
}

if ($controller != '') {
	include("controllers/$controller.php");
}
if (!isset($_POST['onlyController']) || $_POST['onlyController'] == 'false') {
	include("views/$view.php");
}

?>