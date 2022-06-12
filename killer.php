<?php
require_once "include_db_moerder.php";
?>
<!DOCTYPE html>
<html lang="de">
	<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mord am Pfarrerteich</title>
    </head>
    <body>
        <main>
        <?php
        $userID=7;
        $array = [2,7,1,3,9];
        $anzahl=count($array);
        $indexnext=0;
        foreach($array as $el){
            $indexnext++;
            if($indexnext==$anzahl){
                $indexnext=0;
                break;
            }

            if($userID==$el){
                break;
            }
        }

//########################################

        $userID = 20;
        $array = [2,7,1,3,9];

        echo findTarget($userID, $array);


        function findTarget($userID, $hitList){
            array_push($hitList, $hitList[0]); 

            for ($i = 0; $i < count($hitList); $i++) 
            {
                if ($hitList[$i] == $userID){
                    return $hitList[$i+1];
                }
            }

            echo "userID not found!!"
        }

        ?>

        <br>
        
        <br>

        <br>
        </main>
    </body>
</html>