<?php

function GenerateAprooveCodeAndSendMail()
{ global $cam_user;
 $m=$cam_user['mail'];
 $id=$cam_user['identity'];
 $R = mysql_query_my ('delete from cam_aproove where (`mail`="'.$m.'" and identity="'.$id.'")') or die ("Error in GenerateAprooveCodeAndSendMail<br>".mysql_error());
 $code=md5($m.$cam_user['provider']);
 $R = mysql_query_my ('insert into cam_aproove (`mail`,identity,code) values ("'.$m.'","'.$id.'","'.$code.'")') or die ("Error in GenerateAprooveCodeAndSendMail<br>".mysql_error());
}
function GetUserByMail($m)
{ $R = mysql_query_my ('select * from cam_users where (`mail`="'.$m.'")') or die ("Error in GetUserByMail<br>".mysql_error());
 $T=mysql_fetch_array($R);
 if (is_array($T)) {$u=$T;}
  else {$u=array();$u['id']=-1;}
 return $u;
}
function RegistrNewUser()
{ global $cam_user;
 $m=$cam_user['mail'];
 $R = mysql_query_my ('insert into cam_users (`mail`) values ("'.$m.'")') or die ("Error in RegistrNewUser<br>".mysql_error());
 $R = mysql_query_my ('select LAST_INSERT_ID() as id from cam_users') or die ("Error in RegistrNewUser<br>".mysql_error());
 $T=mysql_fetch_array($R);
 if (is_array($T))
 {   $idu=$T['id'];
   $R = mysql_query_my ('select * from cam_users where (`id`='.$idu.')') or die ("Error in GetUserByMail<br>".mysql_error());
   $T=mysql_fetch_array($R);
   $u=$T;
 } else {die('Logic Error');}
 return $u;
}
function RegisterNewOrUpdateUser()
{ global $cam_user;
 $m=$cam_user['mail'];
 $u=GetUserByMail($m);
 if ($u['id']==-1)
   {   	$u=RegistrNewUser();

   }
 if ($cam_user['avatar']!="")  { $u['avatar']=mysql_real_escape_string($cam_user['avatar']);}
 $u['fio']=mysql_real_escape_string($cam_user['fio']);
 $R = mysql_query_my ('update cam_users set fio="'.$u['fio'].'", avatar="'.$u['avatar'].'" where (id='.$u['id'].')') or die ("Error in RegisterNewOrUpdateUser<br>".mysql_error());

 $R = mysql_query_my ('insert into cam_auth (id_user, identity, provider) values ('.$u['id'].',"'.$cam_user['identity'].'","'.$cam_user['provider'].'")') or die ("Error in RegisterNewOrUpdateUser<br>".mysql_error());
}
function TryAprooveEmail($code)
{ global $cam_user;
 $m=$cam_user['mail'];
 $id=$cam_user['identity'];
 $R = mysql_query_my ('select ok from cam_aproove where (`mail`="'.$m.'" and identity="'.$id.'" and code="'.$code.'")') or die ("Error in TryAprooveEmail<br>".mysql_error());
 $T=mysql_fetch_array($R);
 if (isset($T['ok'])&&$T['ok']=="N")
 {
   $R = mysql_query_my ('delete from cam_aproove where (`mail`="'.$m.'" and identity="'.$id.'")') or die ("Error in GenerateAprooveCodeAndSendMail<br>".mysql_error());
   RegisterNewOrUpdateUser();
   header('Location: Aprooved');
   exit;
 }


}
function doCampReg($par)
{
  global $cam_user,$page;
  if ($cam_user['id']!=-1)
  {   header('Location: HOME');
   exit;
  }  $r='Try Reg or <a href="HOME?quit">Log Out</a><br>';
  if (!isset($cam_user['mail'])) {$r.='Sorry, but you can`t login by whith provider ('.$cam_user['provider'].')';}
  else
    {
      if (isset($_GET['code']))
        {
          $code=mysql_real_escape_string($_GET['code']);
          TryAprooveEmail($code);
        }      $r.='You must approve email <a href="SendApprovedEmail">Send approved email</a><br>';
      $r.='<form name="ApprovedEmail" action="Registration" method="GET">';
      $r.='Enter code:<input name="code" type="text" value=""> <input type="submit" value="Approve">';
      $r.='</form>';
      if (!isset($code)) { GenerateAprooveCodeAndSendMail();}
    }

  $r.='<hr>$cam_user<br><pre>'.print_r($cam_user,true).'</pre>';
  $r.='$SESSION<br><pre>'.print_r($_SESSION,true).'</pre>';
  $r.='$page[loginza]<br><pre>'.print_r($page['loginza'],true).'</pre>';



  return $r;
}

?>