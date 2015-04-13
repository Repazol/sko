<?php
  $tpl='index';
  if (isset($_GET['tpl'])) $tpl=$_GET['tpl'];
  $tpl=$tpl.'.tpl';

  if (isset($_GET['do'])) {$do_res=$_GET['do'];}
    elseif (isset($_POST['do'])) {$do_res=$_POST['do'];}
      else {$do_res='';}

  if (isset($_GET['id'])) {$id=$_GET['id'];}
    elseif (isset($_POST['id'])) {$id=$_POST['id'];}
       else {$id=-1;}

  $title='Админка';

  include_once('../inc/cfg/dbconnect.php');
  include_once('../inc/functions.php');
  include_once('../inc/render.php');
  include_once("../inc/admin_tampl.php");

  $co=connect();
  $conf=GetSitePref();
  $tpl=$conf['ADMTMP'];
  if (!isset($_COOKIE['sd'])) {$tpl='auth.tp';}
  $ht=file_get_contents ($tpl) or Die ('Ошибка 001');
  $ht=render($ht);
  echo "$ht";
  mysql_close($co);
?>