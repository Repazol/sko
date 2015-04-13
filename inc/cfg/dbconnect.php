<?php
function connect ()
{
 $sok=mysql_connect("127.0.0.1","root", "") or die (mysql_error());
 mysql_query("SET NAMES 'utf8'");
 mysql_select_db("sko",$sok) or die ('DB:'.mysql_error());
 return $sok;
}
?>
