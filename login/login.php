<?php
session_start();
//Datenbankverbindung
include('dbconnector.inc.php');

//weiterleiten wenn schon eingeloggt
if(isset($_SESSION['login'])){
	header('Location: user.php');
  }
if(isset($_SESSION['perms'])){
  header('Location: admin.php');
}
  

$error = '';
$message = '';


// Formular wurde gesendet und Besucher ist noch nicht angemeldet.
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error)){
	// username
	if(!empty(trim($_POST['username']))){

		$username = trim($_POST['username']);
		
		// prüfung benutzername
		if(!preg_match("/(?=.*[a-z])(?=.*[A-Z])[a-zA-Z]{4,}/", $username) || strlen($username) > 30){
			$error .= "Der Benutzername entspricht nicht dem geforderten Format.<br />";
		}
	} else {
		$error .= "Geben Sie bitte den Benutzername an.<br />";
	}
	// password
	if(!empty(trim($_POST['password']))){
        $password = trim($_POST['password']);

		// passwort gültig?
		if(!preg_match("/(?=^.{6,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/", $password)){
			$error .= "Das Passwort entspricht nicht dem geforderten Format.<br />";
		}
	} else {
		$error .= "Geben Sie bitte das Passwort an.<br />";
	}
	
	// kein fehler
	if(empty($error)){
		// query
		$query = "SELECT id,username, password,perms  from users where username = ?";
		// query vorbereiten
		$stmt = $mysqli->prepare($query);
		if($stmt===false){
			$error .= 'prepare() failed '. $mysqli->error . '<br />';
		}
		// parameter an query binden
		if(!$stmt->bind_param("s", $username)){
			$error .= 'bind_param() failed '. $mysqli->error . '<br />';
		}
		// query ausführen
		if(!$stmt->execute()){
			$error .= 'execute() failed '. $mysqli->error . '<br />';
		}
		// daten auslesen
		$result = $stmt->get_result();
		// benutzer vorhanden
		if($result->num_rows){
			// userdaten lesen
			$row = $result->fetch_assoc();
            // Passwort wird überprüft
          
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
			if(password_verify($password,  $row['password'])){
				$message .= "Sie sind nun eingeloggt";
				
				session_regenerate_id();
				$_SESSION["username"] = $username;

				if($row['perms']=="a"){
					header('Location: admin.php');
					$_SESSION["perms"] = "a";
				}else{
					$_SESSION["login"] = $row['id'];
					header('Location: user.php');	
				}
				

			// benutzername oder passwort stimmen nicht,
			} else {
				$error .= "Benutzername oder Passwort sind falsch";
			}
		} else {
			$error .= "Benutzername oder Passwort sind falsch";
		}
	}
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrierung</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body style="background-image: url('landscape.jpg');background-size:100% auto">>
		<div class="container" style="margin-top:15%;background-color: rgba(200, 225, 233, .6);border-radius:25px;">
			<h1 style=text-align:center;>Login</h1>
			<p style=text-align:center;>
				Bitte melden Sie sich mit Benutzernamen und Passwort an.
			</p>
			<?php
				// fehlermeldung oder nachricht ausgeben
				if(!empty($message)){
					echo "<div class=\"alert alert-success\" role=\"alert\">" . $message . "</div>";
				} else if(!empty($error)){
					echo "<div class=\"alert alert-danger\" role=\"alert\">" . $error . "</div>";
				}
			?>
			<form action="login.php" method="POST" style="width:40%;text-align:center;margin-left:30%;">
				<div class="form-group">
				<label for="username">Benutzername *</label>
				<input type="text" name="username" class="form-control" id="username"
						value=""
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
				</div>
		  		<button type="submit" name="button" value="submit" class="btn btn-info">Login</button>
		  		<button type="reset" name="button" value="reset" class="btn btn-warning">Löschen</button>
			</form>
		</div>
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	</body>
</html>

