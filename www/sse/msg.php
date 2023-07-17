<?php

session_start();

header('Content-Type: text/event-stream');
//header('Cache-Control: no-cache'); // recommended to prevent caching of event data.

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


//$timestamp = '';
//  $oldfile="../audio/.old.txt";
$newfile = "messagesOKApp.txt";


/*
    
    $new = file_get_contents($newfile);
    // $old = 'file_get_contents($oldfile)';
    sendMsg(md5($new), $new);

  */

  $test = date("h:m:s", filemtime($newfile));


while(true) {

  $test2
  = date("h:m:s", filemtime($newfile));

  if($test <= $test2) {
    $new = file_get_contents($newfile);
sendMsg(md5($test2), $test2);
  }

   clearstatcache() ;

  function debug_log($object = null, $label = null)
  {
    $message = json_encode($object, JSON_PRETTY_PRINT);
    $label = "Debug" . ($label ? " ($label): " : ': ');
    echo "<script>console.log(\"$label\", $message);</script>";
  }



  // Break the loop if the client aborted the connection (closed the page)
  if (connection_aborted()) break;

  sleep(3);
}
