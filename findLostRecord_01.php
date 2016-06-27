<?php

 $entries = scandir("./base");
 $dirList = array();
 foreach($entries as $entry) {
    if (strpos($entry, "te") === 0) {
        $filelist[] = $entry;
    }
}

  $dirList = array();
  if($handle = opendir("./base")) 
  {
    while($entry = readdir($handle)) 
    {
      if(is_file($entry)) 
      {
        echo "Current file: ".$entry;
      }

      if(is_dir($entry)) 
      {
        echo "Current dir: ".$entry;
        $dirList[] = $entry;
      }
    }

    closedir($handle);
  }

?>