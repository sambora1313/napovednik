<?php

$connect = mysqli_connect("database", "root", $_ENV['MYSQL_ROOT_PASSWORD'], "napovednik_test");


$ime = $_POST["selected"];

$query = "SELECT * FROM oddaje WHERE ime = '" . $ime . "' LIMIT 1 ";
$oddaja = mysqli_query($connect, $query);
$oddajaPodatki = mysqli_fetch_array($oddaja);

if(!empty($oddajaPodatki)) {

     echo json_encode($oddajaPodatki);

} else {
     echo "Podatka o tej rubriki ni.";
}



?>