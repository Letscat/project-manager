
<?php
session_start();
session_regenerate_id();
include "dbconnector.inc.php";

// Check user login or not
if(isset($_SESSION['perms'])){
  header('Location: admin.php');
}


if(!isset($_SESSION['login']) && !isset($_SESSION['login']) ){
    header('Location: login.php');
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
            //konditionelle Navigationsleiste
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
          <!-- Searchfunktion -->
          <form class="navbar-form navbar-right act" action="archiv.php" method="post">
            <input type="text" class="form-control" name="search" id="search" placeholder="Search...">
            <button type="submit" name="button" value="submit"   class="btn btn-light">Suchen</button>
          </form>
        </div>
      </div>
    </nav>
    
  
          <h2 class="sub-header">Archiv</h2>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Nr</th>
                  <th>Priorität:</th>
                  <th>Kategorie:</th>
                  <th>Auftragname:</th>
                  <th>Erstellt am:</th>
                  <th>Faellig am:</th>
                  <th>Status:</th>
                  <th>Ersteller ID:</th>
                  <th>Ändern:</th>
                  <th>Archivieren:</th>
                  <th>Löschen:</th>
                </tr>
              </thead>
              <tbody>
              <?php
						// select Statement mit LIMIT
            $sql = "SELECT * FROM auftraege";
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

            //query bearbeiten wenn kein
            if(isset($_SESSION['perms'])){

              $error="keine berechtigungen Auftraege anzusehen";
          }

            
            

            $result = $mysqli->query($sql);
            if(empty($error)){
              //standard query für ausgabe der archivierten aufträge
              $sql="select a.id as nr,a.prioritaet as prioritaet,k.kategorienamen as kategorie,a.auftragsname as auftragsname,a.erstelldatum as erstelldatum,a.faellig_am as faellig_am,a.Status as status, a.ersteller as ersteller from auftraege as a Left join kategorien as k on a.id_kategorien =k.id Left join user_has_kategorien as uhk on a.id_kategorien=uhk.id_kategorien WHERE uhk.id_user=".$_SESSION['login']." and a.archiviert is not null  order by faellig_am asc, prioritaet desc Limit ".$limitNr1.", ".$countPerPage." ;";

              //wenn eine Suche gestartet werden soll muss der query bearbeitet werden
              if($_SERVER['REQUEST_METHOD'] == "POST"){
                $search =$_POST['search'];
                echo "<a href='archiv.php' class='btn btn-danger'>Suche abbrechen </a>";
                $sql="select a.id as nr,a.prioritaet as prioritaet,k.kategorienamen as kategorie,a.auftragsname as auftragsname,a.erstelldatum as erstelldatum,a.faellig_am as faellig_am,a.Status as status, a.ersteller as ersteller from auftraege as a Left join kategorien as k on a.id_kategorien =k.id Left join user_has_kategorien as uhk on a.id_kategorien=uhk.id_kategorien WHERE uhk.id_user=".$_SESSION['login']." and a.ersteller=".$_SESSION['login']." and (a.Auftragsname like'%".$search."%' or a.Auftragsbeschreibung like'%".$search."%')order by faellig_am asc,prioritaet desc Limit ".$limitNr1.", ".$countPerPage." ;";
               

              }
              $result = $mysqli->query($sql);
                while($rows = $result->fetch_assoc()) {
                //ausgabe der Auftraege
                  echo  "<tr><td>". $rows['nr']."</td><td>".$rows['prioritaet'] ."</td><td>".$rows['kategorie']."</td><td>".$rows['auftragsname']."</td><td>".$rows['erstelldatum']."</td><td>".$rows['faellig_am']."</td><td>".$rows['status']."</td><td>".$rows['ersteller']."</td><td><a href='edit.php?auftragnr=".$rows['nr']."'>  <i class='fa fa-pencil-square-o' aria-hidden='true'></i> & <i class='fa fa-eye' aria-hidden='true'></i>  </a></td><td><a href='not_archivieren.php?auftragnr=".$rows['nr']."'>nicht mehr Archivieren  </a></td><td><a href='delete_auftrag.php?auftragnr=".$rows['nr']."'> <i class='fa fa-trash' aria-hidden='true'></i> </a></td></tr>";
              }
            }
						?>
              </tbody>
            </table>

            <?php
            //paginierung seitenzahl als variable in get definieren
            for ($page=1;$page<=$numberOfPages;$page++) {
              echo "<a href='user.php?page=". $page ."'> . $page .  </a><p></p>";
            }

            if(!empty($error)){
              echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
            }


            ?>

          </div>
       

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="./Dashboard Template for Bootstrap_files/jquery.min.js.Download"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="./Dashboard Template for Bootstrap_files/bootstrap.min.js.Download"></script>
    <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
    <script src="./Dashboard Template for Bootstrap_files/holder.min.js.Download"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="./Dashboard Template for Bootstrap_files/ie10-viewport-bug-workaround.js.Download"></script>
  

</body></html>