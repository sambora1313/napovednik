<?php
// Set file mime type event-stream
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

// Loop until the client close the stream
while (true) {

  // Echo time
  $time = date(r);
  echo "data: The server time is: {$time}\n\n";

  // Flush buffer (force sending data to client)

    ob_flush();
    flush();

  // Wait 2 seconds for the next message / event
  sleep(20);  
  
}
?>