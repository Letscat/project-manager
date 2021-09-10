<?php

$host = 'localhost';
$database = 'login';
//$username = 'sqldbwr';
//$password = 'XW69_d';
$username = 'root';
$password = '';

// mit datenbank verbinden
$mysqli = new mysqli($host, $username, $password, $database);

// fehlermeldung, falls die Verbindung fehl schlägt.
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_error . ') '. $mysqli->connect_error);
}

?>