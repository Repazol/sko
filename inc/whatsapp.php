<?php

function PutMessage ($tel, $name,$text, $dt)
{
    $tel=mysql_real_escape_string($tel);    $text=addslashes($text);
    $name=mysql_real_escape_string($name);
    $R = mysql_query_my ('insert into whatsapp (fr, name, msg) values ("'.$tel.'","'.$name.'","'.$text.'")') or die ("Error in GetViewbyNAME ('.$name.')<br>".mysql_error());
}
function onGetProfilePicture($from, $target, $type, $data)
{
    $target=str_replace('@s.whatsapp.net','',$target);
    $filename = "../watsapp/profiles/" . $target . ".jpg";
    $fp = @fopen($filename, "w");
    if ($fp) {
        fwrite($fp, $data);
        fclose($fp);
    }
}

function onGetImage($mynumber, $from, $id, $type, $t, $name, $size, $url, $file, $mimetype, $filehash, $width, $height, $preview)
{
    //save thumbnail
    $from=str_replace('@s.whatsapp.net','',$from);
    $previewuri = "../watsapp/media/thumb_" . $file;
    $fp = @fopen($previewuri, "w");
    if ($fp) {
        fwrite($fp, $preview);
        fclose($fp);
    }

    //download and save original
    $data = file_get_contents($url);
    $fulluri = "../watsapp/media/" . $file;
    $fp = @fopen($fulluri, "w");
    if ($fp) {
        fwrite($fp, $data);
        fclose($fp);
    }

   PutMessage ($from,$name,"<a href='$fulluri' target='_blank'><img src='$previewuri' /></a>",$t);
}

function onMessage($mynumber, $from, $id, $type, $time, $name, $body)
{
   $from=str_replace('@s.whatsapp.net','',$from);
   PutMessage ($from,$name,$body,$time);
//    echo "$name:$body<br>$time:<i>$from</i><br>";

}

function WatsAppSendMessage($t,$txt, $rfile, $fl)
{
 global $pref;
 $r='';
 require_once "wp/whatsprot.class.php";
 $w = new WhatsProt($pref['WP_USER'],  $pref['WP_USER'], false); //Name your application by replacing
 try {
 $w->eventManager()->bind("onGetImage", "onGetImage");
 $w->eventManager()->bind("onGetProfilePicture", "onGetProfilePicture");
 $w->eventManager()->bind("onGetMessage", "onMessage");
 $w->connect();
 $w->loginWithPassword($pref['WP_PASSW']);
 $w->sendMessage($t,$txt); // Send Message
 if ($fl!=''&&$rfile!="")
  {
    $target_path = "../watsapp/temp/". basename($rfile);
    if(move_uploaded_file($fl, $target_path)) {
      $fsize='';
      $fhash='';
      $caption='';
      $w->sendMessageImage($t, $target_path, false, $fsize, $fhash, $caption);
  }
 }
 $r.=doNotice('<b>Сообщение отправленно</b>');
} catch (Exception $e) {
    $r.=doNotice('<b>Ошибка:'.$e->getMessage().'</b>');
}
 unset($w);
 return $r;
}
function WatsAppReciveMessage()
{ global $pref;
 $r='';
 require_once "wp/whatsprot.class.php";
 $w = new WhatsProt($pref['WP_USER'],  $pref['WP_USER'], false); //Name your application by replacing
 try {
 $w->eventManager()->bind("onGetImage", "onGetImage");
 $w->eventManager()->bind("onGetProfilePicture", "onGetProfilePicture");
 $w->eventManager()->bind("onGetMessage", "onMessage");
 $w->connect();
 $w->loginWithPassword($pref['WP_PASSW']);
 while($w->pollMessage());
} catch (Exception $e) {
    $r=doNotice('<b>Ошибка:'.$e->getMessage().'</b>');
}
 unset($w);
 return $r;
}
function WASendMessageForm()
{  $r='<fieldset style="width:auto;"><legend>Послать сообщение</legend>';
  $r.='<form enctype="multipart/form-data" action="index.php" method="POST">';
  $r.='<input name="do" type="hidden" value="whatsapp-send">';
  $r.='Номер:<input name="tel" type="text" value=""><br>';
  $r.='<textarea name="mess"></textarea><br>';
  $r.='Файл:<input type="hidden" name="MAX_FILE_SIZE" value="300000" /><input name="fl" type="file" /><br>';
  $r.='<input type="submit" value="Отослать">';
  $r.='</form>';
  $r.='</fieldset>';

  return $r;
}
function WAMessages()
{
  $r='<table>';  $R = mysql_query ('select fr, name, msg from whatsapp order by id desc limit 25') or die ("Error in WAMessages<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
     $T['msg']=str_replace('admin/','',$T['msg']);
     $T['msg']=str_replace('admin239157/','',$T['msg']);
     $r.='<tr><td valign="top" width="100">'.$T['fr'].'</td><td valign="top" width="100">'.$T['name'].'</td><td valign="top">'.$T['msg'].'</td></tr>';
 	 $T=mysql_fetch_array($R);
  }
  $r.='<table>';
  return $r;
}
function WhatsAppAdm()
{
  global $do_res,$pref;
  $pref=GetSitePref();  $r='<b>Whats App</b><br>';
  if ($do_res=="whatsapp-send") {  	   if (isset($_FILES['fl'])) {  	   	  $rf=$_FILES['fl']['name'];  	   	  $f=$_FILES['fl']['tmp_name'];
  	   	  }
  	     else {$f='';$rf='';}  	   $r.=WatsAppSendMessage(addslashes($_POST['tel']),addslashes($_POST['mess']),$rf,$f);
  	   //$r.='POST:'.print_r($_POST,true).' Files:'.print_r($_FILES,true);
  	 }
  if ($do_res=="whatsapp-recive") {$r.=WatsAppReciveMessage();}

  $r.='<table><tr>';
  $r.='<td valign="top" width="300">'.WASendMessageForm().'</td>';
  $r.='<td valign="top"><b>Входящие сообщения</b> <a href="index.php?do=whatsapp-recive">Принять</a><br>'.WAMessages().'</td>';
  $r.='</tr></table>';


  return $r;
}
/* SQL

CREATE TABLE `whatsapp` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`recive` ENUM('Y','N') NOT NULL DEFAULT 'Y',
	`fr` VARCHAR(50) NULL DEFAULT NULL,
	`name` VARCHAR(50) NULL DEFAULT NULL,
	`msg` TEXT NULL,
	`dt` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `rec` (`recive`)
)



*/

?>