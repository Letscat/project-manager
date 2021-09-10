
<?php
session_start();
session_regenerate_id();
include "dbconnector.inc.php";



// Check user login or not
if(!isset($_SESSION['perms'])){
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
$kategorie = '';

//rechte prüfen vor ausführen des Formulars
if(!isset($_SESSION['perms'])){
  $error .="Sie Haben Keine Rechte um Kategorien zu editieren</p><p>";
}


// Wurden Daten mit "POST" gesendet?
if($_SERVER['REQUEST_METHOD'] == "POST"){
  // Ausgabe des gesamten $_POST Arrays
  /*
  echo "<pre>";
  print_r($_POST);
  echo "</pre>";
  */

  // Kategorie vorhanden, mindestens 1 Zeichen und maximal 30 Zeichen lang
  if(isset($_POST['kategorie']) && !empty(trim($_POST['kategorie'])) && strlen(trim($_POST['kategorie'])) <= 30){
    // Spezielle Zeichen Escapen > Script Injection verhindern
    $kategorie = htmlspecialchars(trim($_POST['kategorie']));
  } else {
    // Ausgabe Fehlermeldung
    $error .= "Geben Sie bitte eine korrekte Kategorie ein.<p></p><br />";
  }

  // wenn kein Fehler vorhanden ist, schreiben der Daten in die Datenbank
  if(empty($error)){
    //sql query insert into
    $query = "Insert into kategorien (kategorienamen) values (?)";
    // query vorbereiten
    $stmt = $mysqli->prepare($query);
    if($stmt===false){
      $error .= 'prepare() failed '. $mysqli->error . '<p></p><br />';
    }
    // parameter an query binden
    if(!$stmt->bind_param('s', $kategorie)){
      $error .= 'bind_param() failed '. $mysqli->error . '<p></p><br />';
    }
    
         // query ausführen
    if(!$stmt->execute()){
      $error .= 'execute() failed '. $mysqli->error . '<p></p><br />';
    }
    

   


 
      $message .= "Die Daten wurden erfolgreich in die Datenbank geschrieben<p></p>";
      // felder leeren > oder weiterleitung auf anderes script: z.B. Login!
      $query = $kategorie = '';

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
            //kondionelle NAvigationsleiste
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
      <h1>Kategorien erstellen</h1>
  
      <?php
        // Ausgabe der Fehlermeldungen
        if(!empty($error)){
          echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
        } else if (!empty($message)){
          echo "<div class=\"alert alert-success\" role=\"alert\">" . $message . "</div>";
        }
      ?>
      <form action="kategorien.php" method="post">
        <!-- Kategorie -->
        <div class="form-group">
          <label for="kategorie">Kategorien *</label>
          <input type="text" name="kategorie" class="form-control" id="kategorie"
                  value="<?php echo $kategorie ?>"
                  placeholder="Geben Sie den Kategorienamen an."
                  required>
          </div>
        <button type="submit" name="button" value="submit" class="btn btn-info">Senden</button>
</form>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  </body>
</html>
