<?php
  if ($_SERVER["REQUEST_METHOD"]=="POST"&&!(isset($_POST["docomment"])||isset($_POST["doAuth"])||isset($_POST["LogOut"])))
   {
 	   header("Location: http://download.2gis.ru/arhives/2GISShell-3.13.7.1.msi");
 	   //print_r ($_POST);
 	   exit;
   }

  $ext= '.'.strtolower(substr(strrchr($_GET['s'],'.'),1));
  if ($ext!="."&&$ext!=".xml") {
    header('HTTP/1.1 404 Not Found');
    echo "<html><body>404</body></html>";
    exit;}

  include_once('inc/cfg/dbconnect.php');
  include_once('inc/render.php');
  $co=connect();
  site_render();
  mysql_close($co);
?>