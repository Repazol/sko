<?php

function BlogCNT()
{  $sql='show columns from blog where (Field="cnt")';
  $R = mysql_query ($sql) or die ("Erore in BlogCNT<br>".mysql_error());
  $T=mysql_fetch_array($R);
  if (!is_array($T))
  {    echo "&nbsp;&nbsp;-> Fix blog cnt</br>";
    $R = mysql_query ('ALTER TABLE `blog` ADD COLUMN `cnt` BIGINT NOT NULL DEFAULT "0" AFTER `link`') or die ("Erore in BlogCNT<br>".mysql_error());
  }

}

include_once('cfg/dbconnect.php');
$co=connect();
echo "Database FIX...<br>";
BlogCNT();
echo "Database FIX Done...<br>";
mysql_close($co);

?>