
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
            // kondioneller navbar, zeigt je nach bedingung andere dinge in leiste an
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
      <h1>Benutzerverwaltung</h1>
      <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>BenutzerID:</th>
                  <th>Vorname:</th>
                  <th>Nachname:</th>
                  <th>Benutzername:</th>
                  <th>Email:</th>
                  <th>Berechtigungen:</th>
                  <th>Ändern:</th>
                  <th>Löschen:</th>
                </tr>
              </thead>
              <tbody>
<?php
						// select Statement mit LIMIT
            $sql = "SELECT * FROM users";
            $result = $mysqli->query($sql);
            //anzahl Zeilen total
            $countTotal = $result->num_rows;
            
            //anzahl Zeilen welche pro Seite ausgegeben werden
            $countPerPage = 15;
            
            //Anzahl an seiten berechnen und aufrunden so dass Bei 2,5 seiten 2 Seiten ganz und eine Seite halb gefühlt werden
            $numberOfPages= ceil($countTotal/$countPerPage);
            
            //Links zu unterseiten anzeigen
            
            //bestimmen auf welcher seite der Pagination der Nutzer derzeit ist
            if(!isset($_GET['page'])){
              $page= 1;
            } else {
              $page = $_GET['page'];
            }
            //limit für sql query berechnen
            $limitNr1 = (($page-1)*$countPerPage);
            
            //abfrage der sql daten inklusive dem limit für die pagination
            $sql="select * from users Limit ".$limitNr1.", ".$countPerPage." ;";
            
            $result = $mysqli->query($sql);
            //daten auslesen
            while($rows = $result->fetch_assoc()) {
            //daten ausgeben
              echo  "<tr><td>". $rows['id']."</td><td>".$rows['firstname'] ."</td><td>".$rows['lastname']."</td><td>".$rows['username']."</td><td>".$rows['email']."</td><td>".$rows['perms']."</td><td><a href='edit_user.php?usernr=".$rows['id']."'> <i class='fa fa-pencil-square-o' aria-hidden='true'></i>  </a> </td><td> <a href='delete_user.php?usernr=".$rows['id']."'>  <i class='fa fa-trash' aria-hidden='true'></i> </a></td></tr>";
          }
						?>
              </tbody>
            </table>

            <?php
            //paginierung seitenzahl als variable in get definieren
            for ($page=1;$page<=$numberOfPages;$page++) {
              echo "<a href='admin.php?page=". $page ."'> . $page .  </a>";
            }
            
            ?>
            <p></p>
            <!--  Knopf für neuen nutzer  -->
            <a href='admin2.php' class="btn btn-warning"> Neuen Nutzer erstellen  </a>
          </div>
        </div>
        </body>
</html>