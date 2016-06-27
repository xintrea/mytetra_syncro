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

        findRecordByDirName(0, $root, $entry); // Инит поиска
        $result=findRecordByDirName(1, $root, $entry); // Поиск

        // Если директория записи присутствует в XML-файле
        if( $result==true )
          echo "Ok"."\n";
        else // Иначе директория не отмечена в XML-файле и считается потерянной при синхронизации
        {
          // Имя файла с потерянной записью
          $recordFileName=$dataBaseDir."/".$entry."/text.html";

          // Нужно определить по заголовку HTML-файла, зашифрованный это файл или нет
          $file=fopen($recordFileName,"rb");
          if(!file)
          {
            echo("Ошибка открытия файла ".$recordFileName."\n");
            exit(1);
          }

          $buff=fread ($file, 7);
          if($buff=='RC5SIMP')
          {
            echo "Find lost crypt record in dir ".$entry."\n";
            $cryptRecord[]=$entry;
          }
          else
          {
            echo "Find lost non-crypt record in dir ".$entry."\n";
            $noCryptRecord[]=$entry;
          }
        }
      }
    }

    closedir($handle);
  }


  function findRecordByDirName($mode, $element, $dirName)
  {
    static $findRecord=false;

    // Инит
    if($mode==0)
    {
      $findRecord==false;
      return false;
    }

    if($findRecord==true)
      return $findRecord;

    $nodeId=$element->getAttribute("id"); // echo "Node ID:".$nodeId."\n";

    $recordtableElement=getRecordTable($element);

    // Если у узла есть таблица конечных записей, нужно перебрать записи с целью поиска имени директории
    if($recordtableElement!==false)
      foreach($recordtableElement->getElementsByTagName("record") as $record)
      {
        if($record->getAttribute("dir")==$dirName)
        {
          $findRecord=true;
          return result;
        }
      }

    // Рекурсивный вызов
    foreach($element->childNodes as $currentElement)
      if($currentElement->nodeName==="node")
        findRecordByDirName( $mode, $currentElement, $dirName );

    return $findRecord;
  }


  // Функция находит для переданного узла таблицу конечных записей
  // Возвращает элемент тега <recordtable> или false
  function getRecordTable($element)
  {
    // Определяется, есть ли у узла таблица записей
    foreach($element->childNodes as $childElement)
      if($childElement->nodeName==="recordtable")
        return $childElement;
    
    return false;
  }

?>