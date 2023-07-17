<?php

/*
	Ffmpeg, ki nam normalizira in konvertira datoteke, zaradi specifikacij zapiše id3 tag polje 'COMM' in 'USLT', ki jih uporabljamo v splošno polje 'TXXX'.
	Ta skripta jih po normalizaciji zapiše nazaj na svoje mesto.
	@Jan Gerl - Radio Ognjišče
	*/

// poveži se s podatkovno bazo
require('../connectDB.inc'); 

require_once('/var/www/users/podcast/www/getid3/getid3.php');

// dobi podan argument skripti - relativni naslov datoteke (znotraj mape posnetki/)
$dir = $_GET['oddaja'];
$dir = preg_replace("[^A-Za-z0-9/.]",'',$dir);
$dir = ereg_replace("([\.]{2,})", '', $dir);
$pot = pathinfo($dir);
//$datoteka = $pot['basename'];

//$datoteka="../posnetki/" . $dir;
$datoteka="/var/www/users/podcast/www/posnetki/" . $dir; // uporabi absolutno pot, ker je direktorij link

//Izpis imena datoteke
echo 'Datoteka: ' . $datoteka .'</br>';

//preveri, da je datoteka in ne direktorij:
if (is_dir($datoteka)){
	exit("Napačno podani argumenti - ni datoteka");
}

//dobi ime datoteke, brez končnice
$info = pathinfo($datoteka);

//če ni mp3, preskoči datoteko
if ($info['extension'] != 'mp3'){
	exit("Napačno podani argumenti - ni mp3 datoteka");
}

//polno ime datoteke, brez poti
$id =  basename($datoteka,'.'.$info['extension']);
$ime = explode("_", $id);
$num = count($ime);		
$datum = $ime[1]."-".$ime[2]."-".$ime[3];  //datum = "LLLL-MM-DD"	
$kratica = $ime[0];  //kratica	
$velikost = filesize($datoteka);   // velikost posnetka (v Bytih)

// Initialize getID3 engine
$getID3 = new getID3;
$getID3->setOption(array('encoding'=>'UTF-8'));

// ponastavi spremenljivke
$imeOddaje = $avtor = $picData = $picDesc = $picMime = $picTypeId = $podnaslov = $count = $tagi = $tagiDesc = $povzetek = $opis = "";

// Analyze file and store returned data in $ThisFileInfo
$ThisFileInfo = $getID3->analyze($datoteka);
getid3_lib::CopyTagsToComments($ThisFileInfo);

$imeOddaje = $ThisFileInfo['comments']['album'][0]; //Ime oddaje
//echo "Prvič: ". $imeOddaje ."</br>";
$avtor = $ThisFileInfo['comments']['artist'][0];  // Avtor oddaje
// Polja za sliko
$picData = $ThisFileInfo['comments']['picture'][0]['data'];
$picDesc = $ThisFileInfo['comments']['picture'][0]['description'];
$picMime = $ThisFileInfo['comments']['picture'][0]['image_mime'];
$picTypeId = $ThisFileInfo['id3v2']['APIC'][0]['picturetypeid'];

$podnaslov = $ThisFileInfo['comments']['title'][0];  // Naslov oddaje

//polja za tage
$count = count($ThisFileInfo['comments']['text']);
$tagi = $ThisFileInfo['comments']['text'][$count-1];
$tagiDesc = "";


// Povzetek oddaje - če je povzetek na pravem mestu, ga ponovno zapiši, drugače je datoteka že po normalizaciji in ga vzemi drugje.

if(isset($ThisFileInfo['comments']['comment'][0])){
	$povzetek = $ThisFileInfo['comments']['comment'][0];
}else{
	$povzetek = $ThisFileInfo['tags']['id3v2']['text']['comment'];
}

//Opis oddaje - če je opis na pravem mestu, ga ponovno zapiši, drugače je datoteka že po normalizaciji in ga vzemi drugje.
if(isset($ThisFileInfo['comments']['unsynchronised_lyric'])){
	$opis = $ThisFileInfo['comments']['unsynchronised_lyric'][0];
}else{
	$opis = $ThisFileInfo['tags']['id3v2']['text']['lyrics-eng']; 	// Daljši opis oddaje
}
$opis = "";

//formatiraj trajanje posnetka za v bazo
$cas = $ThisFileInfo['playtime_string']; // playtime in minutes:seconds, formatted string
$t = explode(":", $cas);
if (count($t) > 2){
	$dolzina = "0".$cas;
}else{
	if($t[0]/10 == 0){
		$dolzina = "00:0".$cas;								
	}else{
		$dolzina = "00:".$cas;
	}
}	

$povzetek = str_replace(array('"',"'", "\r","\n"),'', $povzetek);

$podnaslov = str_replace(array('"',"'", "\r","\n"),'', $podnaslov);

// Initialize getID3 tag-writing module
require_once('/var/www/users/podcast/www/getid3/write.php');
$tagwriter = new getid3_writetags;
$tagwriter->filename = $datoteka;
$tagwriter->tagformats = array('id3v2.3');
$tagwriter->tag_encoding = 'UTF-8';

// Pogoji zaradi specifikacij slike (apic) in posebnega custom text	(txxx) polja.
if(!isset($tagi) && (!isset($picData) || !isset($picDesc) || !isset($picMime) || !isset($picTypeId))) //V tagih ni vpisanega polja APIC in TXXX
{
	$TagData = array(
		'album' 				 => array($imeOddaje),
		'artist' 				 => array($avtor),
		'title' 				 => array($podnaslov),
		'unsynchronised_lyric'   => array($opis),			// polje 'USLT'
		'comment'                => array('data' => $povzetek)		// polje 'COMM'
	);
}
else if(!isset($tagi) && (isset($picData) && isset($picDesc) && isset($picMime) && isset($picTypeId))) //V tagih je vpisano polje APIC, a ne TXXX
{
	$TagData = array(
		'album' 				 => array($imeOddaje),
		'artist' 				 => array($avtor),
		'title' 				 => array($podnaslov),
		'unsynchronised_lyric'   => array($opis),			// polje 'USLT'
		'comment'                => array('data' => $povzetek),		// polje 'COMM'
		'attached_picture' 		 => array(array('data' => $picData, 'description' => $picDesc, 'mime' => $picMime, 'picturetypeid' => $picTypeId))
	);

}
else if(isset($tagi) && (!isset($picData) || !isset($picDesc) || !isset($picMime) || !isset($picTypeId))) //V tagih je vpisano polje TXXX, a ne APIC
{
	$TagData = array(
		'album' 				 => array($imeOddaje),
		'artist' 				 => array($avtor),
		'title' 				 => array($podnaslov),
		'unsynchronised_lyric'   => array($opis),			// polje 'USLT'
		'comment'                => array('data' => $povzetek),		// polje 'COMM'
		'text'					 => array(array('data' => $tagi, 'description' => $tagiDesc))
	);
}
else //V tagih je vpisano polje APIC in TXXX
{
	$TagData = array(
		'album'		 			 => array($imeOddaje),
		'artist' 				 => array($avtor),
		'title' 				 => array($podnaslov),
		'unsynchronised_lyric'   => array($opis),			// polje 'USLT'
		'comment'                => array('data' => $povzetek),		// polje 'COMM'
		'attached_picture' 		 => array(array('data' => $picData, 'description' => $picDesc, 'mime' => $picMime, 'picturetypeid' => $picTypeId)),
		'text'					 => array(array('data' => $tagi, 'description' => $tagiDesc))
	);
}
//echo $imeOddaje . "</br>" . $avtor. "</br>" .$podnaslov . "</br>" .$opis . "</br>" .$povzetek . "</br>" . $tagi. "</br>";


// napolni polje za zapis
$tagwriter->tag_data = $TagData;

// write tags to file
if ($tagwriter->WriteTags()) {
	echo 'Uspešno zapisovanje tagov!<br>';
	if (!empty($tagwriter->warnings)) {
		echo 'Opozorila:<br>'.implode('<br><br>', $tagwriter->warnings);
	}
} else {
	echo 'NEUSPEŠNO zapisovanje tagov!<br>'.implode('<br><br>', $tagwriter->errors);
}

//sleep(3);



// mysql query - dobi splošne podatke o ciklu oddaj
$sql = "SELECT * FROM oddaje WHERE kratica='$kratica'";		
$rezultati = $dbh->prepare($sql);
$rezultati->execute();

if($rezultati->rowCount() <= 0){
	echo "Ne morem pridobiti podatkov o ciklu s kratico: ".$kratica;
	exit;
}


// Če podatka ni v tagu, ga pridobi iz default vrednosti v splošnem opisu oddaje
foreach ($rezultati as $row):

if(empty($imeOddaje)){
	$imeOddaje = $row["ime"];
	//echo "Drugič: ". $imeOddaje ."</br>";
}

if(empty($podnaslov)){
	$podnaslov = $imeOddaje;    		//" dne ".intval($ime[3]).". ".intval($ime[2]).". ".intval($ime[1]);
}

if(empty($avtor)){
	$avtor = $row["avtorDefault"];
}

$dirIme = $row["dirIme"];		

$file = '/posnetki/'.$dir;
echo $file . "</br>";		

// Posebne oddaje:
if($kratica == 'nasmeh'){
	$teksti = array("Najkrajša pot med dvema človekoma je smeh.", "Humor je kot dobra začimba: v pravi meri ga lahko dodamo prav vsaki jedi.", "Resnica je jed, ki nikomur ne tekne, zato jo servirajte s humorjem.","Humor je sladkor življenja. Je pa res, da kroži veliko saharina...", "Sreča vstopa v hišo, iz katere se razlega smeh.", "Smehljaj, ki ga nameniš drugemu, se vedno vrne k tebi.", "Nasmeh ne traja več kot trenutek, v spominu pa lahko ostane večno.", "Nasmeh bogati tistega, ki ga prejme, in tistega, ki ga daje.", "Nasmeh je znamenje prijateljstva, dobrote in miru.", "Smeh je najboljše zdravilo proti zastrupljanju duha in srca.", "Kdor se smeji, vleče žeblje iz krste.");
	$podnaslov = $teksti[mt_rand(0, count($teksti) - 1)];
}

if($kratica == 'ura'){
	$ura = intval($ime[4]);
	$podnaslov = "Program Radia Ognjišče od ".$ura.". do ".($ura+1).". ure.";
}

// ukinemo izjemo 2. 10. 2018 Robert
//	 if($kratica == 'info'){					
//povzetek = vse za datumom, brez koncnice
//		$podnaslov = "";
//		for ($i = 4; $i<$num;$i++){
//			$podnaslov = $podnaslov." ".$ime[$i];
//		}							
//	}	

//	if($kratica == 'dm'){					
//		$podnaslov = "Duhovni pomislek";
//		$povzetek = "Kako Gregor Čušin razmišlja ob evangelijskem odlomku današnjega dne.";
//	}

if($kratica == 'vatikan'){					
	$podnaslov = $imeOddaje." dne ".intval($ime[3]).". ".intval($ime[2]).".";
	$povzetek = "Rubriko pripravlja slovensko uredništvo Radia Vatikan.";
}

if($kratica == 'spomin'){					
	$podnaslov = $imeOddaje." dne ".intval($ime[3]).". ".intval($ime[2]).".";
}

if($kratica == 'bbb'){					
	$podnaslov = $imeOddaje." dne ".intval($ime[3]).". ".intval($ime[2]).".";
}


//if($kratica == 'io'){	
//	switch($ime[4]){
//		case "utrip":
//			$podnaslov = "Utrip dneva";
//			break;
//		case "Utrip":
//			$podnaslov = "Utrip dneva";
//			break;
//		case "mozaik":
//			$podnaslov = "Mozaik dneva";
//			break;
//		case "cn":
//			$podnaslov = "Cerkvene novice";
//			break;
//	}				
//}
// posebnosti	


// mysql query - Vstavi v bazo
$sql = "INSERT INTO oddajaDne(id, ime, dirIme, datum, povzetek, fileLink, podnaslov, avtor, velikost, dolzina, tagi, opis) VALUES ('$id', '$imeOddaje', '$dirIme', '$datum', '$povzetek', '$file', '$podnaslov', '$avtor', '$velikost', '$dolzina', '$tagi', '$opis') ON DUPLICATE KEY UPDATE id='$id', ime='$imeOddaje', dirIme='$dirIme', datum='$datum', povzetek='$povzetek', fileLink='$file', podnaslov='$podnaslov', avtor='$avtor', velikost='$velikost', dolzina='$dolzina', tagi='$tagi', opis='$opis'";
$rezultati = $dbh->prepare($sql);
$rezultati->execute();			

endforeach;


//zapri bazo
$dbh = null;

?>
