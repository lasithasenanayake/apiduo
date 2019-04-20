<?php

class st{
    $fp = fopen($file, 'a+');
    stream_set_blocking($fp, 0);
    
    while($count > $loop) {
      if (flock($fp, LOCK_EX)) {
        fwrite($fp, $text);
      }
      flock($fp, LOCK_UN);
    }
    fclose($fp);
}