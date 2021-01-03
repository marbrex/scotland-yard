<?php

$routes = array(
	'home' => array('controller' => '', 'view' => 'home'),
	'show-quartier' => array('controller' => 'afficher_quartier', 'view' => 'quartier'),
	'show-quartier-map' => array('controller' => 'game_tour', 'view' => 'quartier-map'),
	'begin-to-play' => array('controller' => 'submit_form', 'view' => 'begin-to-play'),
	'gameover' => array('controller' => '', 'view' => 'gameover'),
	'victory' => array('controller' => '', 'view' => 'victory')
);

?>