<?php

$connect = mysqli_connect("database", "root", $_ENV['MYSQL_ROOT_PASSWORD'], "napovednik_test");


$podatkiOddaja = $_POST["data"];

$ura = $podatkiOddaja[0];
$tip = $podatkiOddaja[1];
$ime = $podatkiOddaja[2];
$opombe = $podatkiOddaja[3];

$table = 'ponedeljek_dop';


$query = "SELECT * FROM $table WHERE ura = '" . $ura . "' LIMIT 1 ";
$rubrike = mysqli_query($connect, $query);
$rubrikaPodatki = mysqli_fetch_array($rubrike);


$rowVersionNew = $_POST['rowVersionNew'];
$version = $rubrikaPodatki['lastVersion'];

$result = '';

if($rowVersionNew <= $version) {


$result = 0;
     echo json_encode($result);

     echo $result;

} else {

     // check ali obstaja vrstica s to rubriko/oddajo v napovedniku dne
     if (isset($ura) && !empty($rubrikaPodatki)) {

          echo "OBSTAJAM! Ime mi je: ";
          echo $rubrikaPodatki['ura'];

          $update = "UPDATE $table SET ura = '$ura', tip = '$tip', ime = '$ime', opombe = '$opombe', lastVersion = $rowVersionNew WHERE ura = '$ura'";

          if (mysqli_query($connect, $update)) {

               echo " Vrstica v bazi je bila uspešno posodobljena. Tip je: ";
               echo $tip;
          } else {

               echo " Error: " . $update . "<br>" . mysqli_error($connect);
          }
     } else {

          echo "TO BO NOVA VRSTICA. Moje ime je: ";
          echo $ime;

          $novo = "INSERT INTO $table (ura, tip, ime, opombe) VALUES ('$ura', '$tip', '$ime', '$opombe')";

          if (mysqli_query($connect, $novo)) {

               echo " Nova vrstica zapisana v bazo.";
          } else {
               echo "Error: " . $novo . "<br>" . mysqli_error($connect);
          }
     }

}



//$query = "SELECT $ime FROM TABLE $table INSERT INTO $table
//




$insert = "";

//$result = mysqli_query($connect, $insert);

?>