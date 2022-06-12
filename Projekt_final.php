<?php
require_once "include_db_moerder.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">

    <title>Das Mörderspiel</title>
    <!--

TemplateMo 548 Training Studio

https://templatemo.com/tm-548-training-studio

-->
    <!-- Additional CSS Files -->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">

    <link rel="stylesheet" href="assets/css/templatemo-training-studio.css">
    <link rel="stylesheet" href="Projekt_Moerder_CSS.css">

</head>

<body>

    <div id="js-preloader" class="js-preloader">
        <div class="preloader-inner">
            <span class="dot"></span>
            <div class="dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>

    <header class="header-area header-sticky">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">
                        <a href="projekt_final.php" class="logo">Mörder<em> Spiel</em></a>
                        <ul class="nav">
                            <li class="scroll-to-section"><a href="#top" class="active">Home</a></li>
                            <li class="scroll-to-section"><a href="#our-classes">Regeln</a></li>
                            <form method="POST" action="login.php">
                                <input type="submit" value="Admin Login" class="main-button">
                            </form>
                        </ul>
                        <a class='menu-trigger'>
                            <span>Menu</span>
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <div class="main-banner" id="top">
        <video autoplay muted loop id="bg-video">
            <source src="assets/images/pexels-kelly-lacy-6581271.mp4" type="video/mp4" />
        </video>

        <div class="video-overlay header-text">
            <div class="caption">
                <h6>finde dein Opfer, eliminiere es*</h6>
                <h2>Habt Spaß mit unserem <em>Mörderspiel</em></h2>
                <?php



                //       if(isset($_POST["senden"])){		





                $userName = $_GET["userName"];



                $currentContractId = checkUserStatus($userName, $db);



                if (isset($_POST["killConfirmed"])) {
                    $userName = $_GET["userName"];
                    echo $currentContractId;

                    $sql = "UPDATE murdertable SET confirmed = true WHERE MurderID = '$currentContractId'";
                    $abfrage = $db->query($sql); //kill wird auf confirmed gesetzt

                    $userID = getUserId($userName, $db); //user wird auf tot gesetzt
                    $sql = "UPDATE users SET alive = false WHERE UserID = '$userID'";
                    $db->query($sql);

                    $sql = "SELECT targetID FROM murdertable WHERE KillerID = '$userID' AND confirmed = false";
                    $abfrage = $db->query($sql);
                    $newTarget = $abfrage->fetch()[0];


                    $sql = "SELECT killerID FROM murdertable WHERE MurderID = '$currentContractId' ";
                    $abfrage = $db->query($sql);
                    $killer = $abfrage->fetch()[0];

                    $sql = "INSERT INTO MurderTable (KillerId, TargetId) VALUES ( '$killer', '$newTarget')";
                    $abfrage = $db->query($sql);
                    echo "<meta http-equiv='refresh' content='0'>";
                }

                if (isset($_POST["killDenied"])) {
                    $userName = $_GET["userName"];

                    $sql = "UPDATE murdertable SET executed = false WHERE MurderID = '$currentContractId' ";
                    $db->query($sql);
                    echo "<meta http-equiv='refresh' content='0'>";
                    //echo "Congrats";

                }

                if (isset($_POST["joinGame"])) {
                    $userName = $_GET["userName"];

                    $sql = "INSERT INTO users (UserName) VALUES ('$userName')";
                    $db->query($sql);
                    echo "<meta http-equiv='refresh' content='0'>";
                    //echo "Congrats";

                }

                if (isset($_POST["killed"])) {
                    $sql = "UPDATE murdertable SET executed = true WHERE MurderID = '$currentContractId' ";
                    $db->query($sql);
                    echo "Gratuliere zu deinem Mord! Um dein nächstes Ziel zu bekommen, bitte dein Opfer den Mord zu bestätigen";
                }


                function checkUserStatus($userName, $db)
                {


                    $sql = "SELECT CASE WHEN EXISTS(SELECT 1 FROM Users WHERE UserName = '$userName') THEN 1 ELSE 0 END AS DoesUserExist";
                    $abfrage = $db->query($sql);


                    if ($abfrage->fetch()[0] == 0) {
                        //User did not join game yet

                        echo "<h6>" . "Willst du mitspielen?";
                        echo "<form  method = 'post'>
                        <p><input type = 'submit' value='JA, ICH WILL MITSPIELEN' name='joinGame' />
                        </form>";
                        return;
                    }




                    $userID = getUserId($userName, $db);

                    $sql = "SELECT Alive FROM Users WHERE UserID = '$userID'";
                    $abfrage = $db->query($sql);
                    $alive = $abfrage->fetch()[0];

                    if (!$alive) {
                        echo "<h6>" . "Sorry, du wurdest ermordet!" . "</h6>";
                        echo "<p> Für dich ist das Spiel vorbei.. Better luck next time!</p>";
                        return;
                    }






                    $sql = "SELECT MurderID, TargetID, executed, confirmed, KillerID FROM murdertable WHERE KillerID = '$userID' OR TargetID  = '$userID'";

                    $abfrage = $db->query($sql);



                    $maybeDead = False;

                    $dbhits = [];

                    //Ausgehender kill noch nicht confirmed, alles andere muss warten!

                    while ($row = $abfrage->fetch()) {
                        array_push($dbhits, $row);


                        if ($row["KillerID"] == $userID and $row["executed"] == 1 and $row["confirmed"] == 0) {
                            echo "<h6>" . "Gratuliere zu deinem Kill! Dein Opfer hat die Eliminierung noch nicht bestätigt. Bitte die Person auf die Platform zu schauen und dort den Kill zu bestätigen!" . "</h6>" . "<br>";
                            echo "<h6>" . "Solange nicht bestätigt ist, kannst du nicht weiterspielen!" . "</h6>";
                            echo "<p> Du kannst in der Zwischenzeit trotzdem selbst getötet werden, du kannst dein Ableben aber erst bestätigen wenn dein Opfer selbst bestätigt oder abgelehnt hat </p>";
                            return;
                        }
                    }

                    //wenn kein ausgehender unbestätigter kill: Check ob man noch lebt! 

                    foreach ($dbhits as $row) {

                        if ($row["TargetID"] == $userID and $row["executed"] == 1 and $row["confirmed"] == 0) {
                            $maybeDead = True;

                            echo "<h2>" . "Du wurdest getötet? " . "</h2>";
                            echo "bitte antworte ehrlich! ;)";
                            echo "<form  method = 'post'>
                            <input type = 'submit' value='Ja... ich bin tot...  ):' name='killConfirmed' />
                            <input type = 'submit' value='STIMMT GARNICHT' name='killDenied' />
                            </form>";

                            return $row["MurderID"];
                        }
                    }


                    foreach ($dbhits as $row) {

                        if ($row["KillerID"] == $userID and $row["executed"] == 0 and $row["confirmed"] == 0) {
                            echo "<br><br><h6>Hallo " . $userName . ", dein aktuelles Ziel ist " . getUserName($row["TargetID"] . "</h6>" . "<br>", $db); //." - ".$row["MurderID"];


                            echo    "<form  method = 'post'>
                                    <p><input type = 'submit' value='Ich habe den Mord durchgeführt' name='killed' />
                                    </form>";


                            $currentContractId = $row["MurderID"];
                            break;
                        } elseif ($row["executed"] == 1 and $row["confirmed"] == 1) {
                            echo "<h6>" . "Dein Mord an " . getUserName($row["TargetID"], $db) . " wurde bestätigt, weiter gehts!" . "</h6>";
                        }
                    }
                }

                function getUserName($userID, $db)
                {

                    $sql = "SELECT UserName FROM Users WHERE UserID  = '$userID'";

                    $abfrage = $db->query($sql);
                    return $abfrage->fetch()[0];
                }

                function getUserId($userName, $db)
                {

                    $sql = "SELECT UserID FROM Users WHERE UserName  = '$userName'";

                    $abfrage = $db->query($sql);
                    return $abfrage->fetch()[0];
                }
                ?>
                <br><br><br><br>
                <p>*Bitte nicht wirklich, du übergibst deinem Opfer nur einen Gegenstand.</p>
            </div>
        </div>
    </div>
    <section class="section" id="our-classes">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div>
                        <h2>Spielregeln</h2>
                        <br>
                        <p> Das Mörderspiel ist ein Nebenbei-Spiel im Real Life für größere Gruppen (6 bis 166 Spieler:innen) über mehrere Tage mit mindestens drei Auswirkungen:<br>
                            <br>
                        <ul>
                            <li>Man lernt sich untereinander besser kennen</li>
                            <li>Man entwickelt ein gehöriges Maß an Paranoia</li>
                            <li>Man lernt, eine versteckte Agenda mit kreativen Mitteln zu verfolgen</li>
                        </ul>
                        <br>
                        </p>
                        <hr>
                        <p>
                            Alle Mitspielenden erhalten zum Start zufällig einen Mordauftrag z.B. <br>
                            <b>"Erledige Bertram"</b>. Man zieht also los und sucht Bertram, den man erst mal erkennen und dann "umbringen" soll. <br>
                            Nun geht es natürlich (bitte) nicht um einen tatsächlichen Brutalen überfall! <br>
                            <span style="color:white">Das Umbringen funktioniert so: <br>
                                Der Mörder muss seinem Opfer einen gegenstand geben, und das Opfer muss ihn annehmen. <br>
                                Das wars auch schon! <br>
                        </p>
                        </p>
                        <hr>
                        <p>
                            Wichtig dabei:<br>
                            <b>Der Mörder darf seinem Opfer nichts in die Hand drücken oder anders das Annehmen aufzwingen. Der Opfer muss es wirklich selbstständig und Freiwillig entgegen nehmen!</b>
                            <br>
                            Hat man jemanden erfolgreich ermordet, muss man ihm dies sofort sagen. <br>
                            Melde deinen Mord hier auf der Plattform und lasse ihn von deinem Opfer bestätigen (das muss er ebenfalls selber auf hier auf der Platform machen).
                            <br>
                            <br>
                            <b><span style="color:red">Das Opfer ist dann raus aus dem Spiel.</span></b>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p>*Bitte nicht wirklich, du übergibst deinem Opfer nur einen Gegenstand.</p>



                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="assets/js/jquery-2.1.0.min.js"></script>

    <!-- Bootstrap -->
    <script src="assets/js/popper.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    <!-- Plugins -->
    <script src="assets/js/scrollreveal.min.js"></script>
    <script src="assets/js/waypoints.min.js"></script>
    <script src="assets/js/jquery.counterup.min.js"></script>
    <script src="assets/js/imgfix.min.js"></script>
    <script src="assets/js/mixitup.js"></script>
    <script src="assets/js/accordions.js"></script>

    <!-- Global Init -->
    <script src="assets/js/custom.js"></script>

</body>

</html>