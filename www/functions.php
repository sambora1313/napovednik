<?php

$mysqli = mysqli_connect("database", "root", $_ENV['MYSQL_ROOT_PASSWORD'], "napovednik_test");

/* check connection */
if (mysqli_connect_errno()) {
    printf("MySQL connecttion failed: %s", mysqli_connect_error());
} else {
    /* print server version */
    printf("MySQL Server %s", mysqli_get_server_info($mysqli));
}


if (isset($_POST["Import"])) {

    $filename = $_FILES["file"]["tmp_name"];
    if ($_FILES["file"]["size"] > 0) {
        $file = fopen($filename, "r");
        while (($row = fgetcsv($file, 10000, ",")) !== FALSE) {

                $sql = "INSERT INTO oddajaDne(id, ime, dirIme, podnaslov, povzetek, avtor, datum, fileLink, slikaLink, velikost, dolzina, tagi, opis, casObjave) 
                VALUES ('$row[0]', '$row[1]', '$row[2]', '$row[3]', '$row[4]', '$row[5]', '$row[6]', '$row[7]', '$row[8]', '$row[9]', '$row[10]', '$row[11]', '$row[12]', '$row[13]') 
                ON DUPLICATE KEY UPDATE podnaslov='$row[3]', datum='$row[6]', povzetek='$row[4]', fileLink='$row[7]', avtor='$row[5]', velikost='$row[9]', dolzina='$row[10]', tagi='$row[11]', opis='$row[12]'";
            
            $result = mysqli_query($mysqli, $sql);
            if (false === $result) {
                printf("error: %s\n", mysqli_error($mysqli));
            } else {
                print_r($sql);
            }
        }

        fclose($file);
    }
}


/* close connection */
mysqli_close($mysqli);
