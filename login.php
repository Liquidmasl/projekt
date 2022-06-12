<?php
require_once "include_db_moerder.php";
?>
<!DOCTYPE html>
<html lang="de">
	<head>
    <link rel="stylesheet" href="Projekt_Moerder_CSS.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mord am Pfarrerteich</title>
    </head>
    <body>

    </body>
</html>
<div class="container">
<?php
            if(isset($_POST["username"]) && isset($_POST["password"]))
            {
                $username = $_POST["username"];
                $password = $_POST["password"];
                $sql = "SELECT * FROM admins WHERE username='$username' AND password='$password'";
                $ergebnis = $db->query($sql)->fetchAll();

                if (count($ergebnis) > 0) {
                    session_start();
                    $_SESSION["loggedIn"] = true;
                    header('Location: /projekt/admin.php');
                }
                else {
                    echo "Falsche Benutzerdaten";
                }
            }

        ?>

<form action="login.php" method="post">

  <div class="container">
    <label for="username"><b>Username</b></label>
    <input type="text" placeholder="Enter Username" name="username" required>

    <label for="password"><b>Password</b></label>
    <input type="password" placeholder="Enter Password" name="password" required>

    <button type="submit">Login</button>

  </div>
</form>
</div>