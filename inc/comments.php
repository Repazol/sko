<?php

function check_code1($code, $cookie,$cap)
{

// АЛГОРИТМ ПРОВЕРКИ
    $code = trim($code); // На всякий случай убираем пробелы
    $code = md5($code);
// НЕ ЗАБУДЬТЕ ЕГО ИЗМЕНИТЬ!

// Работа с сессией, если нужно - раскомментируйте тут и в captcha.php, удалите строчки, где используются куки
//session_start();
//$cap = $_SESSION['captcha'];
//$cap = md5($cap);
//session_destroy();

    if ($code == $cap){return TRUE;}else{return FALSE;} // если все хорошо - возвращаем TRUE (если нет - false)

}

function MakeComments($par)
{
   global $page;
   $page['head'].='<link rel="stylesheet" href="/css/comments.css" media="all" />';
   $vi1=-1;
   $vi2=-1;
   $err='';$n='';$m='';$u='';$c='';
   if (isset($page['vi1'])) {$vi1=$page['vi1'];if ($vi1=="") $vi1=-1;}   if (isset($page['vi2'])) {$vi2=$page['vi2'];if ($vi2=="") $vi2=-1;}

  if (isset($_POST['docomment']))
  {
     $n=mysql_real_escape_string($_POST['c-name']);
     $m=mysql_real_escape_string($_POST['c-mail']);
     $u=mysql_real_escape_string($_POST['c-url']);
     $c=mysql_real_escape_string($_POST['c-comment']);
     $ip=$_SERVER['REMOTE_ADDR'];

     $c=strip_tags(str_replace('\r','<BR>' ,$c));
     $c=substr($c,0,500);
     include("random.php");
     $cap = $_COOKIE["captcha"]; // берем из куки значение MD5 хэша, занесенного туда в captcha.php
      if (check_code1($_POST['code'], "",$cap))
      {

     $sql='insert into comments (page,vi1,vi2,id_blog,name,mail,url,comment,ip) values ('.$page['id'].','.$vi1.','.$vi2.','.$page['blog-id'].',"'.$n.'","'.$m.'","'.$u.'","'.$c.'","'.$ip.'")';
     $R = mysql_query ($sql) or die ("Error in Cats<br>".mysql_error().'<br>'.$sql);
     } else
       {       	unset($_POST['docomment']);
       	$err='<font color="red">Ошибка: капча введена неверно!</font>';
       }
  }
   $r='<div id="comments" '.$par.'><a name="comments">';
   $r.='<div class="comm-head">Комментарии:</div>';
   //$r.='id:'.$page['id'].' vi1:'.$vi1.' vi2:'.$vi2.' id_blog:'.$page['blog-id'];
   $sql='select name, url, comment from comments where (page='.$page['id'].' and vi1='.$vi1.' and vi2='.$vi2.' and id_blog='.$page['blog-id'].')';
   $R = mysql_query ($sql) or die ("Error in Cats<br>".mysql_error().'<br>'.$sql);
   $T=mysql_fetch_array($R);
   while (is_array($T)) {    $r.='<table width="100%" border="1">';
    $r.='<tr><td class="comm-td-label-name"><b>'.$T['name'].'</b></td><td><u><i>'.$T['url'].'</i></u></td></tr>';
    $r.='<tr><td colspan=2 class="comm-text">'.$T['comment'].'</td></tr>';
    $r.='</table>';
    $T=mysql_fetch_array($R);
   }

  if (!isset($_POST['docomment']))
  {
  $r.='<br><form action="#comments" method="post" enctype="multipart/form-data">';
  $r.='<input name="docomment" type="hidden" value="yes">';
  $r.='<table width="100%">';
  $r.='<tr><td colspan=2><b>Оставте свой комментарий:</b></td></tr>';
  $r.='<tr><td class="comm-td-label">Имя:</td><td><input class="comm-input" name="c-name" type="text" value="'.$n.'"> <sup><font color=red>*</font></sup></td></tr>';
  $r.='<tr><td class="comm-td-label">E-Mail:</td><td><input class="comm-input" name="c-mail" type="text" value="'.$m.'"> <sup><font color=red>*</font></sup></td></tr>';
  $r.='<tr><td class="comm-td-label">URL:</td><td><input class="comm-input" name="c-url" type="text" value="'.$u.'"></td></tr>';
  $r.='<tr><td class="comm-td-label">Капча:</td><td>';
  $r.='<table><tr><td width="220">';
  $r.='<img src="captcha.php" id="capcha-image"><br><a href="javascript:void(0);" onclick="document.getElementById(\'capcha-image\').src=\'captcha.php?rid=\' + Math.random();">Обновить капчу</a>';
  $r.='</td><td><input type="text" name="code"><br>'.$err.'</td></tr></table></td></tr>';

  $r.='<tr><td colspan=2 class="comm-td-label">Комментарий:<br><textarea class="comment-memo" name="c-comment">'.$c.'</textarea></td></tr>';
  $r.='<tr><td style="text-align:right;"></td><td><input type="submit" value="Отправить"></td></tr>';
  $r.='</table>';
  $r.='</form>';
  }
   $r.='</div>';
   return $r;
}

?>