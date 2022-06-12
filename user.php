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
    
<?php
require_once "include_db_moerder.php";

$currentContractId = -1;

//       if(isset($_POST["senden"])){		





    $userName = $_GET["userName"];	
    

    $sql = "SELECT CASE WHEN EXISTS(SELECT 1 FROM Users WHERE UserName = '$userName') THEN 1 ELSE 0 END AS DoesUserExist";
    $abfrage = $db->query($sql);


    

    if ($abfrage->fetch()[0] == 1){
        
        $userID = getUserId($userName, $db);
        

        $sql="SELECT MurderID, TargetID, executed, confirmed, KillerID FROM murdertable WHERE KillerID = '$userID' OR TargetID  = '$userID'";

        $abfrage=$db->query($sql);
        
        

        $maybeDead = False;

        $dbhits = [];

        //Ausgehender kill noch nicht confirmed, alles andere muss warten!
        $readyForMore = true;
        while( $row= $abfrage->fetch() ) {
            array_push($dbhits, $row);
            

            if ($row["KillerID"] == $userID and $row["executed"] == 1 and $row["confirmed"] == 0){
                echo "<h2>YOOOOO, congrats on the kill, your target did not confirm the murder yet tho, please hit them up and tell them to do so</h2>";
                echo "you cant do anything else until they confirmed!!";
                $readyForMore = false;
                break;
            }
            
        }

        //wenn kein ausgehender unbestätigter kill: Check ob man noch lebt! 
        if ($readyForMore){
            foreach( $dbhits as $row ) {                 
                
                if ($row["TargetID"] == $userID and $row["executed"] == 1 and $row["confirmed"] == 0){                          
                    $maybeDead = True;

                    echo "looks like you got killed... is that true?";
                    echo "bitte antworte ehrlich! ;)";
                    echo "<form  method = 'post'>
                        <input type = 'submit' value='Ja.. ich bin tot.. ):' name='killConfirmed' />
                        <input type = 'submit' value='STIMMT NICHT' name='killDenied' />
                        </form>";
                                
                        $currentContractId = $row["MurderID"];  
                        echo $currentContractId;
                        break;

                }
                
            }
            
            

            if (!$maybeDead){
                
                foreach( $dbhits as $row ) {
    
                    if ($row["KillerID"] == $userID and $row["executed"] == 0 and $row["confirmed"] == 0) {
                        echo "your current target is ".getUserName($row["TargetID"], $db)." - ".$row["MurderID"];
                        

                        echo    "<form  method = 'post'>
                                <p><input type = 'submit' value='I KILLED THEEEM' name='killed' />
                                </form>";


                        $currentContractId = $row["MurderID"];  
                        break;
    
                    } elseif ($row["executed"] == 1 and $row["confirmed"] == 1){
                        echo "your kill of .".getUserName($row["TargetID"], $db)."got confirmed, congrats, now continue killing";
    
                    }
                }
            }
        }
    } else {

        echo "Willst du mitspielen?";
        echo "<form  method = 'post'>
                <p><input type = 'submit' value='JAA ICH WILL MITSPIELEN' name='joinGame' />
              </form>";


    }


    


if(isset($_POST["killConfirmed"]))
{
    $userName = $_GET["userName"];
    echo $currentContractId;

    $sql="UPDATE murdertable SET confirmed = true WHERE MurderID = '$currentContractId'";
    $abfrage= $db->query($sql);//kill wird auf confirmed gesetzt
    
    $userID = getUserId($userName, $db);//user wird auf tot gesetzt
    $sql="UPDATE users SET alive = false WHERE UserID = '$userID'";
    $db->query($sql);

    $sql="SELECT targetID FROM murdertable WHERE KillerID = '$userID' AND confirmed = false";
    $abfrage= $db->query($sql);
    $newTarget = $abfrage->fetch()[0];
   
   
    $sql="SELECT killerID FROM murdertable WHERE MurderID = '$currentContractId' ";
    $abfrage= $db->query($sql);
    $killer = $abfrage->fetch()[0];
   
    $sql = "INSERT INTO MurderTable (KillerId, TargetId) VALUES ( '$killer', '$newTarget')";
    $abfrage= $db->query($sql);

}

if(isset($_POST["killDenied"]))
{
    $userName = $_GET["userName"];

    $sql="UPDATE murdertable SET executed = false WHERE MurderID = '$currentContractId' ";
    $db->query($sql);
    echo "Congrats";
    
}

if(isset($_POST["joinGame"]))
{
    $userName = $_GET["userName"];

    $sql="INSERT INTO users (UserName) VALUES ('$userName')";
    $db->query($sql);
    echo "Congrats";
    
}

if(isset($_POST["killed"]))
{
    $sql = "UPDATE murdertable SET executed = true WHERE MurderID = '$currentContractId' ";
    $db->query($sql);
    echo "Congrats";
    
}


function getUserName($userID, $db){

    $sql="SELECT UserName FROM Users WHERE UserID  = '$userID'";

    $abfrage=$db->query($sql);
    return $abfrage->fetch()[0];

}

function getUserId($userName, $db){

    $sql="SELECT UserID FROM Users WHERE UserName  = '$userName'";

    $abfrage=$db->query($sql);
    return $abfrage->fetch()[0];

}
?>