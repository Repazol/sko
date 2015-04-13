<?php
function MakeSqlDump ()
{$dump_dir = "backups"; //$tables = "SHOW TABLES";
$res = mysql_query($tables) or die( "Ошибка при выполнении запроса: ".mysql_error() );
$files=array();
while( $table = mysql_fetch_row($res) )
{
    $fl=$dump_dir."/".$table[0].".sql";
    $files[]=$fl;
    $fp = fopen( $fl, "w" );
    if ( $fp )
    {
        $query = "TRUNCATE TABLE `".$table[0]."`;\n";
        fwrite ($fp, $query);
        $rows = 'SELECT * FROM `'.$table[0].'`';
        $r = mysql_query($rows) or die("Ошибка при выполнении запроса: ".mysql_error());
        while( $row = mysql_fetch_row($r) )
        {
            $query = "";
            foreach ( $row as $field )
            {
                if ( is_null($field) )
                    $field = "NULL";
                else
                    $field = "'".mysql_real_escape_string( $field )."'";
                if ( $query == "" )
                    $query = $field;
                else
                    $query = $query.', '.$field;
            }
            $query = "INSERT INTO `".$table[0]."` VALUES (".$query.");\n";
            fwrite ($fp, $query);
        }
        fclose ($fp);
    }
}
$zip = new ZipArchive();
$filename = 'backups/backup_'.date("Y-m-d(H-i-s)", time()).'.zip';
$r='Файл:'.$filename.'<br>';
if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
    exit("Невозможно открыть <$filename>\n");
}
foreach($files as $key=>$x)
 {
    $x1=str_replace("backups/", "sqls/", $x); 	$zip->addFile($x, $x1);
 	//$r.=$x1.'<br>';
 }

 $dir='../images/';
 if ($dirh = opendir($dir)) {
    while (($file = readdir($dirh)) !== false) {
      if (filetype($dir . $file)!='dir')
        {
         $f=str_replace("../", "", $dir . $file);
         $zip->addFile($dir . $file, $f);
        }
    }
  closedir($dirh);
  }
 $dir='../css/';
 if ($dirh = opendir($dir)) {
    while (($file = readdir($dirh)) !== false) {
      if (filetype($dir . $file)!='dir')
        {
         $f=str_replace("../", "", $dir . $file);
         $zip->addFile($dir . $file, $f);
        }
    }
  closedir($dirh);
  }

$r.='<hr>';
$r.='Файлов: '. $zip->numFiles .'<br>';
$r.='Статус: '. $zip->status;
$zip->close();
foreach($files as $key=>$x)
 {
    unlink ($x);
 }

return $r;
}
function BacupList()
{  $r='<hr>';
 $dir='backups/';
 if ($dirh = opendir($dir)) {
    while (($file = readdir($dirh)) !== false) {
      if (filetype($dir . $file)!='dir')
        {
          $r.='<a href="'.$dir.$file.'">'.$file.'</a><br>';
        }
    }
  closedir($dirh);
  }

  return $r;
}
function MakeBackupInfo()
{
global $do_res,$user_dat;  $r='<b>Резервное копирование</b><br><form name="" action="index.php" method="get">';
  if ($do_res=='back_make') {$r.=MakeSqlDump();}
    else {$r.='<input name="do" type="hidden" value="back_make"><input type="submit" value="Создать копию"></form>'.BacupList();}
  return $r;
}
?>