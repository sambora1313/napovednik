<?php

$connect = mysqli_connect("database", "root", $_ENV['MYSQL_ROOT_PASSWORD'], "napovednik_test");



$query1 = "SELECT * FROM oddaje";
$result1 = mysqli_query($connect, $query1);

$query2 = "SELECT * FROM ponedeljek_dop ORDER by tip";
$result2 = mysqli_query($connect, $query2);

$oddaje = '';

while ($row = mysqli_fetch_array($result1)) {

    $oddaje .= '<option value="' . $row["ime"] . '">' . $row["ime"] . '</option>';

}

$tipi = '';

while ($rowTipi = mysqli_fetch_array($result2)) {

     $tip = $rowTipi["tip"];
     //$tip = rtrim($tip);
  
    $tipi .= '<option value="' . $tip . '">' . $tip . '</option>';

}
   

$inputId = $_POST["input"];
$ura =$_POST["ura"];
//echo $id;

//dobi ime klika in lahko gre v bazo po podatke
$ime = $_POST["ime"];
//echo $ime;

$opombe = $_POST["opombe"];

$output = '';

$oddaja = "SELECT * FROM oddaje WHERE ime = '" . $ime . "' LIMIT 1 ";
$oddajaQuery = mysqli_query($connect, $oddaja);
$oddajaPodatki = mysqli_fetch_array($oddajaQuery);

//print_r($oddajaPodatki);

if (isset($ime) && !empty($oddajaPodatki)) {
        
        $output .= '  
        
                 <table class="table table-bordered">
                <tr>  
                     <td width="30%"><label>Ura</label></td>  
                     <td width="70%" class="ura" contenteditable=true>' . $ura . '</td>  
                </tr>  
                <tr id="tipi">  
                     <td width="30%"><label>Tip oddaje</label></td> 
                     <td width="40%"class="tip drugo"></td> 
                     <td width="30%">
                     <select class="seznam">
                     <option value="Drugo">Drugo ...</option>                   
                     ' . $tipi . ' 
                </select>
                </td>  
                </tr>  
                <tr id="seznamOddaj">  
                     <td width="30%"><label>Ime</label></td> 
                     <td width="40%"class="ime drugo"></td> 
                     <td width="30%">
                      <select class="seznam">
                      <option value="Drugo">Drugo ...</option>
                ' . $oddaje . ' 
                </select>
                     </td>  
                </tr>  
                
                <tr>  
                     <td width="30%"><label>Opombe za pgm</label></td>  
                     <td width="70%" class="opombe" contenteditable=true>' . $opombe . '</td>  
                </tr> 

                <tr>  
                     <td width="30%"><label>Splošni opis oddaje</label></td>  
                     <td width="70%" class="opis">' . $oddajaPodatki["opisOddaje"] . '</td>  
                </tr>

                <tr>  
                     <td width="30%"><label>Aktualni napovednik</label></td>  
                     <td width="70%" class="napovednik" contenteditable=true></td>  
                </tr>  
                
                <tr>  
                     <td width="30%"><label>Podnaslov</label></td>  
                     <td width="70%" class="podnaslov" contenteditable=true></td>  
                </tr> 

                <tr>  
                     <td width="30%"><label>Povzetek</label></td>  
                     <td width="70%" class="povzetek" contenteditable=true></td>  
                </tr> 

                  <tr>  
                     <td width="30%"><label>Avtorji</label></td>  
                     <td width="70%" class="avtor"  contenteditable=true>' . $oddajaPodatki["avtorDefault"] . '</td>  
                </tr> 
  

                </table>
                ';

    
} else {

    $name=$_POST["name"];
    $name=htmlspecialchars($name);

   // echo $name;

    $output .= '<table class="table table-bordered">
                <tr>  
                     <td width="30%"><label>Ura</label></td>  
                     <td width="70%" class="ura" contenteditable=true>' . $ura . '</td>  
                </tr>  
                <tr id="tipi">  
                     <td width="30%"><label>Tip oddaje</label></td> 
                     <td width="40%"class="tip drugo"></td> 
                     <td width="30%">
                     <select class="seznam">
                     <option value="Drugo">Drugo ...</option>                   
                     ' . $tipi . ' 
                </select>
                </td>  
                </tr>  
                <tr id="seznamOddaj">  
                     <td width="30%"><label>Ime</label></td> 
                     <td width="40%"class="ime drugo"></td> 
                     <td width="30%">
                      <select class="seznam">
                      <option value="Drugo">Drugo ...</option>
                ' . $oddaje . ' 
                </select>
                     </td>  
                </tr>  
                
                <tr>  
                     <td width="30%"><label>Opombe za pgm</label></td>  
                     <td width="70%" class="opombe" contenteditable=true>' . $opombe . '</td>  
                </tr> 

                <tr>  
                     <td width="30%"><label>Splošni opis oddaje</label></td>  
                     <td width="70%" class="opis"></td>  
                </tr>

                <tr>  
                     <td width="30%"><label>Aktualni napovednik</label></td>  
                     <td width="70%" class="napovednik" contenteditable=true></td>  
                </tr>  
                
                <tr>  
                     <td width="30%"><label>Podnaslov</label></td>  
                     <td width="70%" class="podnaslov" contenteditable=true></td>  
                </tr> 

                <tr>  
                     <td width="30%"><label>Povzetek</label></td>  
                     <td width="70%" class="povzetek" contenteditable=true></td>  
                </tr> 

                  <tr>  
                     <td width="30%"><label>Avtorji</label></td>  
                     <td width="70%" class="avtor"  contenteditable=true></td>  
                </tr> 
  

                </table>
                ';

}


/* 

if (!empty($id)) {

    $query = "SELECT * FROM ponedeljek_dop WHERE ura = '" . $id . "' LIMIT 1 ";
    $result = mysqli_query($connect, $query);

   
    while ($row = mysqli_fetch_array($result)) {
        $output .= '  
        
                 <table class="table table-bordered">
                <tr>  
                     <td width="30%"><label>Ura</label></td>  
                     <td width="70%" contenteditable=true>' . $row["ura"] . '</td>  
                </tr>  
                <tr>  
                     <td width="30%"><label>Tip oddaje</label></td>  
                     <td width="70%" contenteditable=true><input list="tipi" sytle="width: 100%; value="' . $row["tip"] . '">
                     <datalist id="tipi">' . $tipi . ' 
                </datalist></td>  
                </tr>  
                <tr>  
                     <td width="30%"><label>Ime</label></td>  
                     <td width="70%" contenteditable=true>
                     <input list="oddaje" sytle="width: 100%;" value="' . $row["ime"] . '">
                     <datalist id="oddaje">' . $oddaje . ' 
                </datalist>
                     </td>  
                </tr>  
                <tr>  
                     <td width="30%"><label>Opombe</label></td>  
                     <td width="70%" contenteditable=true>' . $row["opombe"] . '</td>  
                </tr>   

                </table>
                ';
    }

    
} else {

    $name=$_POST["name"];
    $name=htmlspecialchars($name);

   // echo $name;

    $output .= '<table class="table table-bordered">
                <tr>  
                     <td width="30%"><label>Ura</label></td>  
                     <td width="70%" contenteditable=true>' . $name . '</td>  
                </tr>  
                <tr>  
                     <td width="30%"><label>Tip oddaje</label></td>  
                     <td width="70%" contenteditable=true><input list="tipi" sytle="width: 100%;">
                     <datalist id="tipi">' . $tipi . ' 
                </datalist></td>  
                </tr>  
                <tr>  
                     <td width="30%"><label>Ime</label></td>  
                     <td width="70%">
                     <input list="oddaje" sytle="width: 100%;">
                     <datalist id="oddaje">' . $oddaje . ' 
                </datalist>
                </td>
                </tr>  
                <tr>  
                     <td width="30%"><label>Opombe</label></td>  
                     <td width="70%" contenteditable=true></td>  
                </tr>   

                </table>
                ';

}
*/

echo $output;

/*
$name = '';
$inputId = '';
$ime = '';
*/
?>