<?php
function GetFID ($filial, $id_user)
{  $r=-1;$e='';
  $sql='select id from sko_filials where (id_user="'.$id_user.'" and name="'.$filial.'")';
  $R = mysql_query_my ($sql) or $e='err';
  if ($e=="")
  {
     $T=mysql_fetch_array($R);
     if (is_array($T)) {
       $r=$T['id'];
     }
  }
  return $r;
}
function GetFilialID ($filial, $id_user)
{
  $e='';  $r=GetFID ($filial, $id_user);
  if ($r==-1)
  {    $sql='insert into sko_filials (id_user,name) values ("'.$id_user.'", "'.$filial.'")';
    $R = mysql_query_my ($sql) or $e='err';
    if ($e=="") {$r=GetFID ($filial, $id_user);}
  }
  return $r;
}
function GetPlID ($place, $id_filial)
{
  $r=-1;$e='';
  $sql='select id from sko_places where (id_filial="'.$id_filial.'" and name="'.$place.'") order by id limit 1 ';
  $R = mysql_query_my ($sql) or $e='err';
  if ($e=="")
  {
     $T=mysql_fetch_array($R);
     if (is_array($T)) {
       $r=$T['id'];
     }
  }
  return $r;
}
function GetPlaseID ($place, $id_filial)
{
  $e='';
  $r=GetPlID ($place, $id_filial);
  if ($r==-1)
  {
    $sql='insert into sko_places (id_filial,name) values ("'.$id_filial.'", "'.$place.'")';
    $R = mysql_query_my ($sql) or $e='err';
    if ($e=="") {$r=GetPlID ($place, $id_filial);}
  }
  return $r;
}
function SkoGetQuestInfo($par)
{ $r='';
 $id=GetPostGetParamSTR("q");
 $id=str_replace("-","",$id);
 $error="";

 $filial=GetPostGetParamSTR("f");
 $us=GetPostGetParamSTR("us");
 $ps=GetPostGetParamSTR("ps");
 if ($filial=="") {$error="Не указан филиал";}
 $place=GetPostGetParamSTR("p");
 if ($place=="") {$error="Место не указанно";}

 $sql='select sko_quests.id, sko_quests.id_user, sko_quests.name,sko_quests.`change` from sko_quests
 inner join auth_users on auth_users.id=sko_quests.id_user
 where (UNIX_TIMESTAMP(sko_quests.cod)='.$id.' and (auth_users.username="'.$us.'" or auth_users.mail="'.$us.'") and auth_users.pass="'.$ps.'")';
 $R = mysql_query_my ($sql) or $error="Internal server error".mysql_error();
 $T=mysql_fetch_array($R);
 if (is_array($T)&&$error=='')
 {
   $idq=$T['id'];
   $id_user=$T['id_user'];
   $id_filial=GetFilialID ($filial, $id_user);
   if ($id_filial==-1) {$error="Внутренняя ошибка сервера";}
     else {$id_place=GetPlaseID($place,$id_filial);}
   if ($id_place==-1) {$error="Внутренняя ошибка сервера";}

   $r.='<div class="id-place" id="id-place">'.$id_place.'</div>';
   $r.='<div class="quest-name" id="quest-name">'.$T['name'].'</div>';
   $r.='<div class="quest-change" id="quest-change">'.$T['change'].'</div>';
   $n=0;
   $sql='select id,question,answers,pos,tmpl,bgcolor,btncolor,qfont,afont, q_offs, a_offs,`condition` from sko_questions where (id_q='.$idq.' and is_del="N") order by pos';
   $R = mysql_query_my ($sql) or $error="Internal server error".mysql_error();
   $T=mysql_fetch_array($R);
   while (is_array($T))
   {
     $keys=array_keys($T);
     for ($i=0;$i<count($keys);$i++)
   	 {
   	   if ($i % 2 != 0)
   	   {
   	   $T[$keys[$i]]=str_replace(chr(13),'|',$T[$keys[$i]]);
   	   $T[$keys[$i]]=str_replace(chr(10),'',$T[$keys[$i]]);
       $r.='<div id="question-'.$n.'-'.$keys[$i].'">'.$T[$keys[$i]].'</div>';
       }
     }
     $n++;
     $T=mysql_fetch_array($R);
   }

 }
  else {if ($error=="") {$error="Код указан не верно";}}

 $r.='<div id="error">'.$error.'</div>';


 return $r;
}

?>