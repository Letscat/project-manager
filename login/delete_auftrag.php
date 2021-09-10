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
//ersteller des auftrages abfragen
$sql="select a.ersteller as ersteller from auftraege as a Left join kategorien as k on a.id_kategorien =k.id where a.id=".$_GET['auftragnr'].";";


//werte auslesen und in variablen definieren so dass sie danach in ein leeres formular eingefüllt werden können
$result = $mysqli->query($sql);
$row = $result->fetch_assoc(); 
$ersteller= $row['ersteller'];

if($ersteller != $_SESSION["login"]) {
    $error .="Sie Haben Keine Rechte um den Auftrag zu löschen</p><p>";
  
  }

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


$sql="delete from auftraege where id=".$auftrag.";";


//keine Fehlermeldung
if(empty($error) && isset($_SESSION['login'])){
    if ($mysqli->query($sql) === TRUE) {
        echo "Auftrag erfolgreich gelöscht";
        header( "refresh:1.5;url=user.php" );
    } else {
        echo "Löschen des Auftrags fehlgeschlagen " . $mysqli->error;
        header( "refresh:1.5;url=user.php" );
    }
}else {
    echo "Keine Berechtigungen zum löschen " . $mysqli->error;
    header( "refresh:3;url=user.php" );
}
?>