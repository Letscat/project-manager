
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
$firstname = $lastname = $email = $username =$email =$password = $perms = '';
//Abfrage des Users der Bearbeitet wird
$sql="select * from users where id=".$_GET['usernr'].";";

//werte auslesen und in variablen definieren so dass sie danach in ein leeres formular eingefüllt werden können
$result = $mysqli->query($sql);
$row = $result->fetch_assoc(); 
$firstname = $row['firstname'];
$lastname = $row['lastname'];
$username = $row['username'];
$email = $row['email'];
$perms =$row['perms'];
$id =$row['id'];

//rechte prüfen vor ausführen des Formulars
if(!isset($_SESSION['perms'])){
  $error .="Sie Haben Keine Rechte um den Nutzer zu editieren</p><p>";
}





// Wurden Daten mit "POST" gesendet?
if($_SERVER['REQUEST_METHOD'] == "POST"){
  // Ausgabe des gesamten $_POST Arrays
  /*
  echo "<pre>";
  print_r($_POST);
  echo "</pre>";
  */

  // vorname vorhanden, mindestens 1 Zeichen und maximal 30 Zeichen lang
  if(isset($_POST['firstname']) && !empty(trim($_POST['firstname'])) && strlen(trim($_POST['firstname'])) <= 30){
    // Spezielle Zeichen Escapen > Script Injection verhindern
    $firstname = htmlspecialchars(trim($_POST['firstname']));
  } else {
    // Ausgabe Fehlermeldung
    $error .= "Geben Sie bitte einen korrekten Vornamen ein.<br /></p><p>";
  }

  // nachname vorhanden, mindestens 1 Zeichen und maximal 30 zeichen lang
  if(isset($_POST['lastname']) && !empty(trim($_POST['lastname'])) && strlen(trim($_POST['lastname'])) <= 30){
    // Spezielle Zeichen Escapen > Script Injection verhindern
    $lastname = htmlspecialchars(trim($_POST['lastname']));
  } else {
    // Ausgabe Fehlermeldung
    $error .= "Geben Sie bitte einen korrekten Nachnamen ein.<br /></p><p>";
  }

  if(isset($_POST['perms']) && !empty(trim($_POST['perms'])) && strlen(trim($_POST['perms'])) <= 30){
    // Spezielle Zeichen Escapen > Script Injection verhindern
    $perms = htmlspecialchars(trim($_POST['perms']));
  }else
  $error .= "Geben Sie bitte einen korrekten rang ein.<br /></p><p>";

  // emailadresse vorhanden, mindestens 1 Zeichen und maximal 100 zeichen lang
  if(isset($_POST['email']) && !empty(trim($_POST['email'])) && strlen(trim($_POST['email'])) <= 100){
    $email = htmlspecialchars(trim($_POST['email']));
    // korrekte emailadresse?
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false){
      $error .= "Geben Sie bitte eine korrekte Email-Adresse ein<br /></p><p>";
    }
  } else {
    // Ausgabe Fehlermeldung
    $error .= "Geben Sie bitte eine korrekte Email-Adresse ein.<br /></p><p>";
  }

  // benutzername vorhanden, mindestens 4 Zeichen und maximal 30 zeichen lang
  if(isset($_POST['username']) && !empty(trim($_POST['username'])) && strlen(trim($_POST['username'])) <= 30){
    $username = trim($_POST['username']);
    // entspricht der benutzername unseren vogaben (minimal 4 Zeichen, Gross- und Kleinbuchstaben)
		if(!preg_match("/(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{4,}/", $username)){
			$error .= "Der Benutzername entspricht nicht dem geforderten Format.<br /></p><p>";
		}
  } else {
    // Ausgabe Fehlermeldung
    $error .= "Geben Sie bitte einen korrekten Benutzernamen ein.<br /></p><p>";
  }

  // passwort vorhanden, mindestens 6 Zeichen
  if(isset($_POST['password']) && !empty(trim($_POST['password']))){
    //passwort hash
    $password_unhashed = trim($_POST['password']);
    $password = password_hash($password_unhashed, PASSWORD_DEFAULT);
    //entspricht das passwort unseren vorgaben? (minimal 6 Zeichen, Zahlen, Buchstaben, keine Zeilenumbrüche, mindestens ein Gross- und ein Kleinbuchstabe)
    if(!preg_match("/(?=^.{6,}$)((?=.*\d+)(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password_unhashed)){
      $error .= "Das Passwort entspricht nicht dem geforderten Format.<br /></p><p>";
    }
  } else {
    // Ausgabe Fehlermeldung
    $error .= "Geben Sie bitte einen korrekten Nachnamen ein.<br /></p><p>";
  }



  // wenn kein Fehler vorhanden ist, schreiben der Daten in die Datenbank
  if(empty($error)){

    //löschen der Kategorien so dass diese neu definiert werden können
    
    $sql="delete from user_has_kategorien where id_user=".$id.";";
    

//keine Fehlermeldung beim löschen der Kategorien eines nutzers
if(empty($error) && isset($_SESSION['perms'])){
    if ($mysqli->query($sql) === TRUE) {
        $message.= "Alte Berechtigungen erfolgreich gelöscht</p><p>";
        header( "refresh:4;url=admin.php" );
    } else {
        $error .= "Löschen des Auftrags fehlgeschlagen " . $mysqli->error."</p><p>";
        header( "refresh:4;url=admin.php" );
    }
}



    //firstname, lastname, username, password, email
    $query = "UPDATE users SET firstname=?, lastname=?, username=?, password=?, email=?,perms=? where id=".$id.";";
    
    // query vorbereiten
    $stmt = $mysqli->prepare($query);
    if($stmt===false){
      $error .= 'prepare() failed '. $mysqli->error . '<br /></p><p>';
    }
    // parameter an query binden
    if(!$stmt->bind_param('ssssss', $firstname, $lastname, $username, $password, $email, $perms)){
      $error .= 'bind_param() failed '. $mysqli->error . '<br /></p><p>';
    }
    
         // query ausführen
    if(!$stmt->execute()){
      $error .= 'execute() failed '. $mysqli->error . '<br /></p><p>';
    }
    //übergabe der  berechtigungen eines nutzers in verschiedenen kategorien
          // kategorien zuweisen
          
          $result0 = $mysqli->query("SELECT id  FROM users where username=\"$username\";");
          $row = $result0->fetch_assoc();
          $id_user = $row['id'];
          

          $result1 = $mysqli->query("select id,kategorienamen from kategorien;");
          while($rows = $result1->fetch_assoc()) {
              $category = $rows['kategorienamen'];
              $id_category = $rows['id'];
              if(isset($_POST[$category])){

                $sql = "INSERT INTO user_has_kategorien (id_user, id_kategorien)
                VALUES ($id_user, $id_category)";
                
                if ($mysqli->query($sql) === TRUE) {
                  $message .= "Nutzer in Kategorien eingewiesen </p><p>";
                } else {
                  $error .= "Error: " . $sql . "<br>" . $mysqli->error."</p><p>";
                }
                
              }
             
          }


    // kein Fehler!
    if(empty($error)){
      $message .= "Die Daten wurden erfolgreich in die Datenbank geschrieben<br/ >";
      // felder leeren > oder weiterleitung auf anderes script: z.B. Login!
      $username = $password = $firstname = $lastname = $email = $perms = $sql = $user_id = $category = $id_category = '';
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
            if(isset($_SESSION['perms'])){

              //konditioneller navbar
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
  
      <?php
        // Ausgabe der Fehlermeldungen
        if(!empty($error)){
          echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
        } else if (!empty($message)){
          echo "<div class=\"alert alert-success\" role=\"alert\">" . $message . "</div>";
        }
      ?>
      <form action="" method="post">
        <!-- vorname -->
        <div class="form-group">
          <label for="firstname">Vorname *</label>
          <input type="text" name="firstname" class="form-control" id="firstname"
                  value="<?php echo $firstname ?>"
                  placeholder="Geben Sie Ihren Vornamen an."
                  required>
        </div>
        <!-- nachname -->
        <div class="form-group">
          <label for="lastname">Nachname *</label>
          <input type="text" name="lastname" class="form-control" id="lastname"
                  value="<?php echo $lastname ?>"
                  placeholder="Geben Sie Ihren Nachnamen an"
                  maxlength="30"
                  required>
        </div>
        <!-- email -->
        <div class="form-group">
          <label for="email">Email *</label>
          <input type="email" name="email" class="form-control" id="email"
                  value="<?php echo $email ?>"
                  placeholder="Geben Sie Ihre Email-Adresse an."
                  maxlength="100"
                  required>
        </div>
        <!-- benutzername -->
        <div class="form-group">
          <label for="username">Benutzername *</label>
          <input type="text" name="username" class="form-control" id="username"
                  value="<?php echo $username ?>"
                  placeholder="Gross- und Keinbuchstaben, min 4 Zeichen."
                  maxlength="30" required
                  pattern="(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{4,}"
                  title="Gross- und Keinbuchstaben, min 4 Zeichen.">
        </div>
        <!-- password -->
        <div class="form-group">
          <label for="password">Password *</label>
          <input type="password" name="password" class="form-control" id="password"
                  placeholder="Gross- und Kleinbuchstaben, Zahlen, Sonderzeichen, min. 6 Zeichen, keine Umlaute"
                  pattern="(?=^.{6,}$)((?=.*\d+)(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$"
                  title="mindestens einen Gross-, einen Kleinbuchstaben, eine Zahl und ein Sonderzeichen, mindestens 6 Zeichen lang,keine Umlaute."
                  required>

                  <div class="form-group">
          <label for="perms">Berechtigungen *</label>
          <select name="perms" id="perms">
          <option value="u">User</option>
          <option value="a">Admin</option>
          <option value="<?php echo $perms ?>" selected="selected"> <?php echo $perms ?> </option>
          </select><p>  </p>
          </div>

          
          <?php
          //checkbox für kategorien
          include('dbconnector.inc.php');

          $result = $mysqli->query("select id,kategorienamen from kategorien;");
          ?>
          Kategorien <p>  </p>
          <?php
          while($rows = $result->fetch_assoc()) {
              $category = $rows['kategorienamen'];
              $id_category = $rows['id'];
              echo"<input type='checkbox' name='$category' value='$id_category'> $category <p></p>";
          }
          ?>
        

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
