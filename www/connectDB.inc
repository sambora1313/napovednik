<?php 

	try{
		$dbh = new PDO("mysqli:host=localhost;dbname=napovednik_test;charset=utf8", "docker", "docker");
	}catch(PDOException $e){
		echo "Napaka pri zapisovanju: " - $e->getMessage();
	} 
	
?>