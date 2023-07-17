<?php

// set headers for stream
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Access-Control-Allow-Origin: *");

/**
 * Constructs the SSE data format and flushes that data to the client.
 *
 * @param string $id Timestamp/id of this connection.
 * @param string $msg Line of text that should be transmitted.
 **/
// Loop until the client close the stream

function sendMsg($id, $msg)
{
    echo "id: $id" . PHP_EOL;
    echo "data: $msg" . PHP_EOL;
    echo PHP_EOL;
    ob_flush();
    flush();
}


$newfile = "https://radio.ognjisce.si/audio/messagesOKApp.txt?ref=tools.ognjisce.si";


$t = md5($newfile);

$file1 = $t;
sendMsg(md5('test'), $t);

// ko je povezava pošljem stanje.
$song = file_get_contents($newfile);


if (isset($song)) {
    sendMsg(md5('test'), $song);
    unset($song);
}


// while connection to client open
while (true) {

    // check file timestamp every time 
    $file2 = md5($newfile);


    if ($file1 == $file2) {

       // $new_song = file_get_contents($newfile);
        sendMsg(md5('test'), $file);
        //sendMsg(md5('test'), $new_song);
        //$file1 = $file2;
    }

    // Break the loop if the client aborted the connection (closed the page)
    if (connection_aborted()) break;

    sleep(5);
}