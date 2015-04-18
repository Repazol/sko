<?php

function AuthGetUserBySID ($u)
{
  if ($u['sid']!="NOSID")
  {
   $ip=getClientIP();   $sql='select id, username,mail from auth_users where (sid="'.$u['sid'].'" and ip="'.$ip.'")';
   $R = mysql_query_my ($sql) or die ("Error in GetUserBySID<br>".mysql_error());
   $T=mysql_fetch_array($R);
   if (is_array($T)) {
    $u['id']=$T['id'];
    $u['sid']=md5(rand(1,50000));
    $u['user']=$T['username'];
    $u['mail']=$T['mail'];
    mysql_query_my ('update auth_users set sid="'.$u['sid'].'" where (id='.$u['id'].')') or die ('Error in GetUserBySID'.mysql_error());
  	setcookie('sid',$u['sid']);
   }
  }
  return $u;
}
function AuthTryLogin ($u)
{
  $ip=getClientIP();
  $us=GetPostGetParam('username');
  $sql='select id, username,mail from auth_users where ((username="'.$us.'" or mail="'.$us.'") and pass="'.GetPostGetParam('password').'")';
  $R = mysql_query_my ($sql) or die ("Error in AuthTryLogin<br>".mysql_error());
  $T=mysql_fetch_array($R);
  if (is_array($T)) {
    $u['id']=$T['id'];
    $u['sid']=md5(rand(1,50000));
    $u['user']=$T['username'];
    $u['mail']=$T['mail'];
    mysql_query_my ('update auth_users set sid="'.$u['sid'].'", ip="'.$ip.'" where (id='.$u['id'].')') or die ('Error in AuthTryLogin'.mysql_error());
  	setcookie('sid',$u['sid']);
  } else {$u['error']='<font color=red>Login or passwoed incorect</font>';}
  return $u;
}
function AuthLogOut($u)
{
  $sql='update auth_users set sid="NOSID" where (sid="'.$u['sid'].'")';
  $R = mysql_query_my ($sql) or die ("Error in AuthLogOut<br>".mysql_error());
  $u['sid']='NOSID';  setcookie('sid','');
}
function AuthCreateNullUser()
{  $u=array();
  $u['id']=-1;
  $u['sid']="NOSID";
  $u['user']="";
  $u['mail']='';

  if (isset($_GET['LogOut'])||isset($_POST['LogOut']))
  {    AuthLogOut($u);
  }
    else
    {
      if (isset($_COOKIE['sid'])) {  	     $u['sid']=mysql_real_escape_string($_COOKIE['sid']);
    	 $u=AuthGetUserBySID ($u);
    }

    if (isset($_POST['doAuth']))
    {      $u=AuthTryLogin ($u);
    }
   }



  return $u;

}
function AuthINIT($par)
{  global $page;
  $r='';
  $page['user']=AuthCreateNullUser();
  return $r;
}
function AuthInfo($par)
{  global $page;
  if (isset($page['user'][$par])) {$r=$page['user'][$par];} else {$r='';}
  return $r;
}
function AuthShowAuthInfoTag ($tag)
{
  global $page;
  $page['vars']['username']=$page['user']['user'];  include_once('engine.php');
  return GetTagbyNAME ($tag);
}
function AuthDefForm($u='',$p='')
{  $r='<form name="" action="HOME" method="post">';
  $r.='<input name="doAuth" type="hidden" value="KJG">';
  $r.='Пользователь:<input name="username" type="text" value="'.$u.'"> ';
  $r.=' Пароль:<input name="password" type="password" value="'.$p.'"> ';
  $r.='<input type="submit" value="Вход">';
  $r.='</form>';
  return $r;

}
function Auth($par)
{  global $page;
  $r='';
  if (isset($page['user']))
  {
    $tags=explode(' ',$par);
    //$r.=print_r($tags,true);
    if ($page['user']['id']!=-1)
      {$r.=AuthShowAuthInfoTag($tags[0]);}
        else {$r.=AuthShowAuthInfoTag($tags[1]);}

  } else
    {$r.='Not run AUTH-INIT';}

  if (isset($page['user']['error'])) {$r.=$page['user']['error'];}
  return $r;
}

function check_code($code, $cookie,$cap)
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

function AuthRegForm($par,$err)
{ global $page;
 $r='';
 $u=mysql_real_escape_string(GetPostGetParam('r-username'));
 $m=mysql_real_escape_string(GetPostGetParam('r-mail'));
 $cap='<img src="captcha.php" id="capcha-image"><br><a href="javascript:void(0);" onclick="document.getElementById(\'capcha-image\').src=\'captcha.php?rid=\' + Math.random();">Обновить капчу</a>';
 if ($par=="") {
 $r.='<form autocomplete="off" name="reg" action="" method="post"><input name="doAuth" type="hidden" value="doReg"><input name="docomment" type="hidden" value="doReg"><table>';
     $r.='<tr><td style="text-align:right;width:210px;"><b>Имя польователя:</b></td><td style="width:210px;"><input style="width:200px;" name="r-username" type="text" value="'.$u.'"></td><td rowspan="5">'.$err.'</td></tr>';
     $r.='<tr><td style="text-align:right;"><b>E-Mail:</b></td><td><input style="width:200px;" name="r-mail" type="text" value="'.$m.'"></td></tr>';
     $r.='<tr><td style="text-align:right;"><b>Пароль:</b></td><td><input style="width:200px;" name="r-pass1" type="password" value=""></td></tr>';
     $r.='<tr><td style="text-align:right;"><b>Ещё раз:</b></td><td><input style="width:200px;" name="r-pass2" type="password" value=""></td></tr>';
     $r.='<tr><td style="text-align:right;"><b>Капча:</b></td><td>'.$cap.'<br><input type="text" name="code"></td></tr>';
     $r.='<tr><td style="text-align:right;"><b></b></td><td><input type="submit" value="Зарегистрировать"></td></tr>';
     $r.='';
     $r.='';
     $r.='</table></form>';
 } else
   {   	 $r=GetTagbyNAME ($par);
   	 $page['vars']['username']=$u;
   	 $page['vars']['mail']=$m;
   	 $page['vars']['capcha']=$cap;
   	 $page['vars']['error']=$err;
   }
 return $r;
}
function AuthGetUserIDByField($f,$u)
{
  $r=-1;
  $sql='select id from auth_users where ('.$f.'="'.$u.'")';  $R = mysql_query_my ($sql) or die ("Error in AuthGetUserIDByField<br>".mysql_error());
  $T=mysql_fetch_array($R);
  if (is_array($T))
  {  	$r=$T['id'];
  }
  return $r;
}
function AuthDoReg($par)
{
  global $page;  $r='';
  $u=mysql_real_escape_string(GetPostGetParam('r-username'));
  $m=mysql_real_escape_string(GetPostGetParam('r-mail'));
  $p1=mysql_real_escape_string(GetPostGetParam('r-pass1'));
  $p2=mysql_real_escape_string(GetPostGetParam('r-pass2'));
  //$r='u:'.$u.'<br>m:'.$m.'<br>';
  $good=true;
  if (AuthGetUserIDByField('username',$u)!=-1)
  {  	$r.=AuthRegForm($par,'<br><font color="red">Имя пользователя занято</font>');
  	$good=false;
  }
  /*if (AuthGetUserIDByField('mail',$m)!=-1||!isValidMail($m)&&$good)
  {
  	$r.=AuthRegForm($par,'<br><br><br><br><font color="red">E-Mail уже используется или указан не правильно</font>');
  	$good=false;
  } */
  if (($p1!=$p2)&&$good)
  {
  	$r.=AuthRegForm($par,'<br><br><br><br><font color="red">Пароли не идентичны</font>');
  	$good=false;
  }
  if (strlen($p1)<5&&$good)
  {
  	$r.=AuthRegForm($par,'<br><br><br><br><font color="red">Пароль меньше 6 символов</font>');
  	$good=false;
  }
  if ($good)
  {  	$r.='Регистрация прошла успешно!<br>'.AuthDefForm($u,$p1);
    $sql='insert into auth_users (username,pass,mail) values ("'.$u.'","'.$p1.'","'.$m.'")';
    $R = mysql_query_my ($sql) or die ("Error in AuthDoReg<br>".mysql_error());
  }

  return $r;//.'<pre>'.print_r($page['sqls'],true).'</pre>Mail:('.print_r(isValidMail('mail@mail.com'),true).')('.print_r(isValidMail('mailmail.com'),true).')'.isValidMail($m);
}
function AuthReg ($par)
{  global $page;
  $r='';

  if (isset($page['user'])&&$page['user']['id']!=-1)
  {  	$r.='Для регистрации нового пользователя, необходимо закончить сесию текущего пользователя. <a href="?LogOut">Выход</a>';
  }
    else
    {
      if (isset($_POST['docomment'])&&$_POST['docomment']=="doReg")
      {
        include("random.php");
        $cap = $_COOKIE["captcha"]; // берем из куки значение MD5 хэша, занесенного туда в captcha.php
        if (check_code($_POST['code'], "",$cap))
        {           $err='';
           $r.=AuthDoReg($par);
        } else {$r.=AuthRegForm($par,'<br><br><br><br><br><br><br><font color="red">Капча введена не верно!</font>');}
      } else {$r.=AuthRegForm($par,'');}
    }
  //$r.='<pre>'.print_r ($_POST,true).'</pre>';
  return $r;
}

?>