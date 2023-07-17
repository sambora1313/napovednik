<?php

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache'); // recommended to prevent caching of event data.

/**
 * Constructs the SSE data format and flushes that data to the client.
 *
 * @param string $id Timestamp/id of this connection.
 * @param string $msg Line of text that should be transmitted.
 **/
// Loop until the client close the stream

function sendMsg($id, $msg, $msg2)
{
  echo "id: $id" . PHP_EOL;
  echo "data: $msg" . PHP_EOL;
  echo "data: $msg2" . PHP_EOL;
  echo PHP_EOL;
  echo PHP_EOL;
  ob_flush();
  flush();
}

$newfile = "../messagesOKApp.txt";


$t = date("h:m:s", filemtime($newfile));
clearstatcache();
$time1 = $t;

$new_song = file_get_contents($newfile);
sendMsg(md5('test'), $new_song, 'samo prvič test');

while (true) {

 $time2 = date("h:m:s", filemtime($newfile));
  clearstatcache();

  if ($time1 == $time2) {
    //sendMsg(md5('test1'), $t, $t2);
  } else {
    $new_song = file_get_contents($newfile);
    sendMsg(md5('test'), $new_song, 'test');
    $time1 = $time2;
  }

  // Break the loop if the client aborted the connection (closed the page)
  if (connection_aborted()) break;

  sleep(3);
}
