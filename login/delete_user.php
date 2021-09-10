<?php
session_start();
session_regenerate_id();
include "dbconnector.inc.php";
// Check user login or not
if(!isset($_SESSION['perms'])){
    header('Location: noperms.php');
}

//initalisierung
$user=$error='';

//Entgegennahme und validierung usernr

if(!empty(trim($_GET['usernr'])) && strlen(trim($_GET['usernr'])) <= 30){
    $user = trim($_GET['usernr']);

    // besteht die priorität aus zahlen
		if(!preg_match('/^[0-9]*$/', $user)){
			$error .= "Die Usernr entspricht nicht dem Geforderten Format.<br />";
		}
  } else {
    // Ausgabe Fehlermeldung
    $error .= "Geben Sie bitte eine korrekte Usernr ein.<br />";
  }

//nutzer löschen query
$sql="delete from users where id=".$user.";";
//kategorien löschen query
$sql2="delete from user_has_kategorien where id_user=".$user.";";
//auftraege von Nutzer löschen query
$sql3="delete from auftraege where ersteller=".$user.";";


//keine Fehlermeldung
if(empty($error) && isset($_SESSION['perms'])){
    //löschung durchführen
    if ($mysqli->query($sql) === TRUE) {
        echo "Nutzer erfolgreich gelöscht</p><p>";
        header( "refresh:1.5;url=admin.php" );
    } else {
        echo "Löschen des Benutzers fehlgeschlagen </p><p>" . $mysqli->error;
        header( "refresh:1.5;url=admin.php" );
    }

    if ($mysqli->query($sql2) === TRUE) {
        echo "Nutzer berechtigungen erfolgreich gelöscht</p><p>";
        header( "refresh:1.5;url=admin.php" );
    } else {
        echo "Löschen der Nutzerberechtigungen fehlgeschlagen </p><p> " . $mysqli->error;
        header( "refresh:1.5;url=admin.php" );
    }

    if ($mysqli->query($sql3) === TRUE) {
        echo "Auftraege von Nutzer gelöscht</p><p>";
        header( "refresh:1.5;url=admin.php" );
    } else {
        echo "Löschen der Aufträge des Benutzers fehlgeschlagen </p><p>" . $mysqli->error;
        header( "refresh:1.5;url=admin.php" );
    }
    header( "refresh:1.5;url=admin.php" );
}
?>