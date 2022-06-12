<?php
require_once "include_db_moerder.php";
?>
<!DOCTYPE html>
<html lang="de">
	<head>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">

    <link rel="stylesheet" href="assets/css/templatemo-training-studio.css">
    <link rel="stylesheet" href="Projekt_Moerder_CSS.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Das Mörderspiel - Admin</title>
    </head>
    <body>

    </body>
</html>
<div class="container">
  <div class="row justify-content-md-center">

    <div class="col">
    <main>
            <h1>Das Mörderspiel Übersicht Admin</h1>
        <h3>Spieler hinzufügen:</h3>
        <form  method = "post">
		   <p><input name="name"> Name</p>
           <p><input type = "submit" name="senden" />
           <p class="mdbutton"><input type = "submit" value="START NEW GAME (CAUTION)" name="newGame" onclick="return confirm('Spiel starten?')" /> <!--JS-Popup?-->
        </form>
        <?php
            session_start();
            if (!isset($_SESSION["loggedIn"])) {
                header('Location: /projekt/login.php');
            }

            if(isset($_POST["senden"]))
            {
                $name = $_POST["name"];
                $sql="INSERT INTO users (UserName) VALUES ('$name')";
                //echo $sql;
                $db->query($sql);
            }
        
            $sql = "SELECT * FROM users";

            $abfrage=$db->query($sql);
            $deadOrAlive=["dead","alive"];

            $userArray = [];
            while( $row= $abfrage->fetch() ) {
                array_push($userArray, $row);
                echo $row["UserID"]. "     \t\t\t" . $row["UserName"] . "     \t\t\t" . $deadOrAlive[$row["Alive"]]. "<br>";		
            }


            if(isset($_POST["newGame"])){
                $sql="UPDATE Users SET alive = true";
                $db->query($sql);

                $sqlDel = "TRUNCATE MurderTable";
                $db->query($sqlDel);


                shuffle($userArray);

                echo "<br><br><br>";
                echo "CONTRACT-LIST:<br>";

                array_push($userArray,$userArray[0]);

                
                for ($i = 0; $i < count($userArray) - 1; $i++)
                {

                                       
                    $killer = $userArray[$i]["UserID"];
                    $target = $userArray[$i+1]["UserID"];
                    echo $userArray[$i]["UserName"]."-".$userArray[$i]["UserID"]. " --> " ;

                    $sql = "INSERT INTO MurderTable (KillerId, TargetId) VALUES ( '$killer', '$target')";
                    $db->query($sql);

                }

            }

        ?>

        <br>
        
        <br>

        <br>
        </main>
    </div>
  </div>
</div>