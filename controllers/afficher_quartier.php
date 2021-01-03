<?php

//======================== Quartier ========================//

// La requete pour l'affichage des quartiers sous format d'un tableau HTML
$req = "SELECT * FROM (SELECT idQ, nomQ, nomC FROM scotlandyard_project.quartier q NATURAL JOIN scotlandyard_project.commune c) q INNER JOIN scotlandyard_project.route r ON q.idQ=r.idQDepart ORDER BY idQ;";
$resultReqQuartier = mysqli_query($connection, $req);

// La requete pour l'affichage des quartiers sous format de carte Leaflet
$req = "SELECT nomQ, coordsQ FROM scotlandyard_project.quartier;";
$resultCoordsQ = mysqli_query($connection, $req);

?>
