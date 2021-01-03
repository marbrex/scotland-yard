<?php

$websiteName = "Scotland Yard";
$login = "root";
$pwd = "";
$server = "localhost";
$tableDB = "scotlandyard_project";

function write_log ($mesg) {
	$file = "logs/log.txt";
	$mesg .= "\n";
	file_put_contents($file, $mesg, FILE_APPEND);
}

function show_log ($nbl) {
	$logs = file("logs/log.txt");
	foreach ($logs as $i => $line) {
		if ( $i >= (sizeof($logs) - $nbl) ) {
			echo "Ligne ".($i+1)." : ".htmlspecialchars($line)."<br />\n";
		}
	}
}

?>