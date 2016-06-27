<?php
 
  $dataBaseDir="./base";
  $mytetraXml="./mytetra.xml";

  // Получение DOM-дерева корневого файла
  $dom = new DomDocument;
  if( !$dom->load($mytetraXml) )
  {
    echo "Bad XML file ".$mytetraXml."\n";
    exit(1);
  }   

  // Корневой элемент XML-документа
  $root=$dom->documentElement->getElementsByTagName("content")->item(0);

  $noCryptRecord=array();
  $cryptRecord=array();

  // Перебор списка каталогов с записями
  $dirList = array();
  if($handle = opendir($dataBaseDir)) 
  {
    while($entry = readdir($handle)) 
    {
      if(is_dir($dataBaseDir."/".$entry)) 
      {
        echo "Check dir: ".$entry."\n";

        $result=findRecordByDirName($root, $entry);

        // Если директория записи присутствует в XML-файле
        if( $result['isExists']==true )
          echo "Ok"."\n";
        else
        {
          // Иначе директория не отмечена в XML-файле и считается потерянной при синхронизации

          // Нужно определить по заголовку HTML-файла, зашифрованный это файл или нет

        }
          
      
      }
    }

    closedir($handle);
  }







  function findRecordByDirName($element, $dirName)
  {
    $nodeId=$element->getAttribute("id");
    echo "Node ID:".$nodeId."\n";

    $recordtableElement=getRecordTable($element);

    // Если у узла есть таблица конечных записей, нужно выкачивать каталоги с записями
    if($recordtableElement!==false)
      foreach($recordtableElement->getElementsByTagName("record") as $record)
        downloadRecord($record);

    // Рекурсивный вызов
    foreach($element->childNodes as $currentElement)
      if($currentElement->nodeName==="node")
        if($currentElement->getAttribute("crypt")==="1")
          continue;
        else
          downloadRecurse( $currentElement );
  }


?>