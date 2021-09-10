<?php
session_start();
session_regenerate_id();
include "dbconnector.inc.php";
// Check user login or not
if(!isset($_SESSION['login'])){
    header('Location: noperms.php');
}

//initalisierung
$auftrag=$error='';

//Entgegennahme und validierung usernr

if(!empty(trim($_GET['auftragnr'])) && strlen(trim($_GET['auftragnr'])) <= 11){
    $auftrag = trim($_GET['auftragnr']);

    // besteht die priorität aus zahlen
		if(!preg_match('/^[0-9]*$/', $auftrag)){
			$error .= "Die Auftragnr entspricht nicht dem Geforderten Format.<br />";
		}
  } else {
    // Ausgabe Fehlermeldung
    $error .= "Geben Sie bitte eine korrekte Auftragnr ein.<br />";
  }

//Auftrag wird archiviert
$sql="update auftraege Set archiviert = true where id=".$auftrag.";";


//keine Fehlermeldung
if(empty($error) && isset($_SESSION['login'])){
    if ($mysqli->query($sql) === TRUE) {
        echo "Auftrag erfolgreich Archiviert";
        header( "refresh:1.5;url=user.php" );
    } else {
        echo "Archivieren des Auftrags fehlgeschlagen " . $mysqli->error;
        header( "refresh:1.5;url=user.php" );
    }
}
?>