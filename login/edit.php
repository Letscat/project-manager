
<?php
session_start();
session_regenerate_id();
include "dbconnector.inc.php";

// Check user login or not
if(!isset($_SESSION['login'])){
  header('Location: user.php');
}
// logout
if(isset($_POST['but_logout'])){
    session_destroy();
    header('Location: login.php');
}
?>


<?php

//verbindung zur Datenbank Auslagern
include('dbconnector.inc.php');

// Initialisierung
$error = $message =  '';
$auftragname =$auftragdesc = $priority = $category = $date = $date_todo = $status = '';
//abfragen der Werte des Bettroffenen Auftrages
$sql="select a.id as nr,a.prioritaet as prioritaet,k.kategorienamen as kategorie,a.id_kategorien as id_kategorie,a.auftragsname as Auftragsname,a.Auftragsbeschreibung as Auftragsbeschreibung,a.erstelldatum as erstelldatum,a.faellig_am as faellig_am,a.Status as status, a.ersteller as ersteller from auftraege as a Left join kategorien as k on a.id_kategorien =k.id where a.id=".$_GET['auftragnr'].";";

//werte auslesen und in variablen definieren so dass sie danach in ein leeres formular eingefüllt werden können
$result = $mysqli->query($sql);
$row = $result->fetch_assoc(); 
$priority = $row['prioritaet'];
$auftragname = $row['Auftragsname'];
$auftragdesc = $row['Auftragsbeschreibung'];
$kategorie = $row['kategorie'];
$id_kategorie = $row['id_kategorie'];
$date = $row['erstelldatum'];
$date_todo = $row['faellig_am'];
$status= $row['status'];
$ersteller= $row['ersteller'];

//rechte prüfen vor ausführen des Formulars
if(!isset($_SESSION['login'])){
  $error .="Sie Haben Keine Rechte um den Auftrag zu editieren</p><p>";
}
if($ersteller != $_SESSION["login"]) {
  $error .="Sie Haben Keine Rechte um den Auftrag zu editieren</p><p>";

}


// Wurden Daten mit "POST" gesendet?
if($_SERVER['REQUEST_METHOD'] == "POST"){
  /* Ausgabe des gesamten $_POST Arrays
  echo "<pre>";
  print_r($_POST);
  echo "</pre>";
  */

  //Auftragsnamen gesetzt, maximal 50 zeichen lang
  if(isset($_POST['auftragname']) && !empty(trim($_POST['auftragname'])) && strlen(trim($_POST['auftragname'])) <= 50){
    // Spezielle Zeichen Escapen > Script Injection verhindern
    $auftragname = htmlspecialchars(trim($_POST['auftragname']));
  } else {
    // Ausgabe Fehlermeldung
    $error .= "Geben Sie bitte einen korrekten Auftragsname ein.<br />";
  }

//Auftragsbeschreibung maximal 5000 zeichen mindestens 1
  if(isset($_POST['auftragdesc']) && !empty(trim($_POST['auftragdesc'])) && strlen(trim($_POST['auftragdesc'])) <= 5000){
    // Spezielle Zeichen Escapen > Script Injection verhindern
    $auftragdesc = htmlspecialchars(trim($_POST['auftragdesc']));
  } else {
    // Ausgabe Fehlermeldung
    $error .= "Geben Sie bitte eine Korrekte Auftragsbeschreibung ein.<br />";
  }

  // priorität vorhanden, exakt ein zeichen
  if(isset($_POST['priority']) && !empty(trim($_POST['priority'])) && strlen(trim($_POST['priority'])) == 1){
    $priority = trim($_POST['priority']);

    // besteht die priorität aus zahlen
		if(!preg_match('/^[0-5]*$/', $priority)){
			$error .= "Die Priorität entspricht nicht dem geforderten Format.<br />";
		}
  } else {
    // Ausgabe Fehlermeldung
    $error .= "Geben Sie bitte eine korrekte Priorität ein.<br />";
  }

    // kategorie vorhanden, exakt ein zeichen
    if(isset($_POST['category']) && !empty(trim($_POST['category'])) && strlen(trim($_POST['category'])) <= 20){
      $category = trim($_POST['category']);
  
      // besteht die kategorie_id aus zahlen
      if(!preg_match('/^[0-9]*$/', $category)){
        $error .= "Die Kategorie entspricht nicht dem geforderten Format.<br />";
      }
    } else {
      // Ausgabe Fehlermeldung
      $error .= "Geben Sie bitte eine korrekte Kategorie ein.<br />";
    }



       // Datum vorhanden, exakt 10 zeichen
       if(isset($_POST['date']) && !empty(trim($_POST['date'])) && strlen(trim($_POST['date'])) == 10){
        $date = trim($_POST['date']);
    
        // besteht das Datum aus zahlen und "-"
        if(!preg_match('/^[0-9-]*$/', $date)){
          $error .= "Das Datum entspricht nicht dem geforderten Format.<br />";
        }
      } else {
        // Ausgabe Fehlermeldung
        $error .= "Geben Sie bitte eine korrektes Datum ein.<br />";
      }
  
       // Datum todo vorhanden, exakt 10 zeichen
       if(isset($_POST['date_todo']) && !empty(trim($_POST['date_todo'])) && strlen(trim($_POST['date_todo'])) == 10){
        $date_todo = trim($_POST['date_todo']);
    
        // besteht das Datum aus zahlen und "-"
        if(!preg_match('/^[0-9-]*$/', $date_todo)){
          $error .= "Das Datum_todo entspricht nicht dem geforderten Format.<br />";
        }
      } else {
        // Ausgabe Fehlermeldung
        $error .= "Geben Sie bitte eine korrektes Datum ein.<br />";
      }

  // Status vorhanden, maximal 3 zeichen

  if(isset($_POST['status']) && !empty(trim($_POST['status'])) && strlen(trim($_POST['status'])) <= 20){
    $status = trim($_POST['status']);

    // besteht der Status aus Zahlen
		if(!preg_match('/^[0-9]*$/', $status)){
			$error .= "Der Status entspricht nicht dem geforderten Format.<br />";
		}
  } else {
    // Ausgabe Fehlermeldung
    $error .= "Geben Sie bitte eine korrekten Status ein.<br />";
  }  

  // wenn kein Fehler vorhanden ist, schreiben der Daten in die Datenbank
  if(empty($error)){
    //firstname, auftragdesc, priority, password, email
    $query = "UPDATE auftraege SET Auftragsname=?, Auftragsbeschreibung=?, prioritaet=?, id_kategorien=?, erstelldatum=?, faellig_am=?, Status=? where id =".$_GET['auftragnr'].";";
    
    // query vorbereiten
    $stmt = $mysqli->prepare($query);
    if($stmt===false){
      $error .= 'prepare() failed '. $mysqli->error . '<br />';
    }
    // parameter an query binden
    if(!$stmt->bind_param('sssssss', $auftragname, $auftragdesc, $priority, $category, $date, $date_todo, $status)){
      $error .= 'bind_param() failed '. $mysqli->error . '<br />';
    }

    // query ausführen
    if(!$stmt->execute()){
      $error .= 'execute() failed '. $mysqli->error . '<br />';
    }
    // kein Fehler!
    if(empty($error)){
      $message .= "Die Daten wurden erfolgreich in die Datenbank geschrieben<br/ >";
      // felder leeren > oder weiterleitung auf anderes script: z.B. Login!
      $auftragname =$auftragdesc = $priority = $category = $date = $date_todo = $status = '';
      // verbindung schliessen
      $mysqli->close();
    }

  }
}

?>

<!DOCTYPE html>
<!-- saved from url=(0053)# -->
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
    <link rel="icon" href="https://getbootstrap.com/docs/3.3/favicon.ico">
    <link rel="canonical" href="#">

    <title>Aufgabenmanagement Tool</title>

    <!-- Bootstrap core CSS -->
    <link href="./Dashboard Template for Bootstrap_files/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="./Dashboard Template for Bootstrap_files/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="./Dashboard Template for Bootstrap_files/dashboard.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="./Dashboard Template for Bootstrap_files/ie-emulation-modes-warning.js.Download"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
  
  <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Aufgabenmanagement</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            
            <?php
            // konditioneller navbar
            if(isset($_SESSION['perms'])){
              echo "
              <li><a href='admin.php'>Benutzerverwaltung</a></li>
              <li><a href='kategorien.php'>Kategorien</a></li>
            
              ";
            }
            if(isset($_SESSION['login'])){
              echo "
            <li><a href='user.php'>Übersicht</a></li>
            <li><a href='auftrag.php'>Auftraege</a></li>
            <li><a href='archiv.php'>Archiv</a></li>
            
              ";
            }



              ?>
              <li><a>Eingeloggt als: <?php echo $_SESSION['username']  ?> </a></li>
            <li><a href='logout.php'>Logout <i class="fa fa-sign-out" aria-hidden="true"></i></a></li>
          </ul>
        
        </div>
      </div>
    </nav>

    <div class="container">
      <h1>Auftrag erstellen</h1>
      <p>
        Hier können sie Aufträge erstelle.
      </p>
      <?php
        // Ausgabe der Fehlermeldungen
        if(!empty($error)){
          echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
        } else if (!empty($message)){
          echo "<div class=\"alert alert-success\" role=\"alert\">" . $message . "</div>";
        }
      ?>
<form action="" method="post">
        <!-- Auftragname -->
        <div class="form-group">
          <label for="auftragname">Auftragname *</label>
          <input type="text" name="auftragname" class="form-control" id="auftragname"
                  value="<?php echo $auftragname ?>"
                  placeholder="Geben sie den Auftragsnamen ein"
                  required>
        </div>

         <!-- Auftragdesc -->
         <div class="form-group">
          <label for="auftragdesc">Auftragbeschreibung *</label>
          <textarea id="auftragdesc" required name="auftragdesc" class="form-control" rows="8" cols="50" > <?php echo $auftragdesc ?> </textarea>
          
        </div>

        <!-- dropdown für Priorität -->
        <div class="form-group">
          <label for="priority">Priorität *</label>
          <select name="priority" id="priority" >
                <option value="1">1(nicht dringend)</option>
                <option value="2">2 (relativ dringend)</option>
                <option value="3">3 (dringend)</option>
                <option value="4">4 (sehr dringend)</option>
                <option value="5">5 (werft alles über den haufen und rennt)</option>
                <option value="<?php echo $priority ?>" selected="selected"> <?php echo $priority ?> </option>
               </select> 


        </div>
        <?php

include "dbconnector.inc.php";
$sql= "select uhk.id_kategorien as id,k.kategorienamen as kategorienamen from user_has_kategorien as uhk left join kategorien as k on uhk.id_kategorien=k.id where uhk.id_user=".$_SESSION["login"] ." order by k.kategorienamen asc;";
$result = $mysqli->query($sql);
?>
<div class="form-group">
        <label for="category">Kategorie *</label>
<select name="category" id="category">
<?php
include "dbconnector.inc.php";
//kategorien aus db als dropdown darstellen 
while($rows = $result->fetch_assoc()) {
    $category = $rows['kategorienamen'];
    echo '<option value="'.$rows['id'].'">'.$category.'</option>';
}
?>
<option value="<?php echo $id_kategorie ?>" selected="selected"> <?php echo $kategorie ?> </option>
</select>





        </div>
        
        <!-- Datum erstellt am -->
        <div class="form-group">
          <label for="date">Erstellt Am *</label>
          <input type="date" id="date" name="date" value="<?php echo $date ?>">
                
        </div>

        <!-- Datum Faellig am -->
        <div class="form-group">
          <label for="date_todo">Faellig AM *</label>
          <input type="date" id="date_todo" name="date_todo" value="<?php echo $date_todo ?>">
                
        </div>
        <!-- Statusanzeige -->
        <div class="form-group">
          <label for="status">Status *</label>
          <input type="range" min="0" max="100" value="<?php echo $status ?>" id="status" name="status">

 
        </div>
        <button type="submit" name="button" value="submit" class="btn btn-info">Senden</button>
        <button type="reset" name="button" value="reset" class="btn btn-warning">Löschen</button>
      </form>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  </body>
  
</html>
