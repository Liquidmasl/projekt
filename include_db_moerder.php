<?php
$dbHost="localhost";
$dbCharset="utf8";
$dbName="projekt";//ggf ändern!!
$dbUser="root";//ggf ändern!!
$dbPw="";//ggf ändern!!

try
{	
	//Ein Verbindungs-Objekt aus der Klasse PDO erstellen
	//dieses hält zahlreiche "hauseigene" Funktionen (Methoden) bereit query, fetch...
	$db = new PDO(
		"mysql:host=$dbHost;dbname=$dbName;charset=$dbCharset",
		$dbUser,
		$dbPw
		);

}
catch(PDOException $e)
{
	var_dump($e);
	exit("Keine Verbindung zur Datenbank");
}

?>