<?php

function GetAnsID ($id_q,$answ)
{
  $r=-1;$e='';
  $sql='select id from sko_answers where (id_quest="'.$id_q.'" and txt="'.$answ.'")';
  $R = mysql_query_my ($sql) or $e='err';
  if ($e=="")
  {
     $T=mysql_fetch_array($R);
     if (is_array($T)) {
       $r=$T['id'];
     }
  }
  return $r;
} //id,id_quest,txt

function GetAnswerID ($id_q,$answ)
{  $e='';
  $r=GetAnsID ($id_q,$answ);
  if ($r==-1)
  {
    $sql='insert into sko_answers (id_quest,txt) values ("'.$id_q.'", "'.$answ.'")';
    $R = mysql_query_my ($sql) or $e='err';
    if ($e=="") {$r=GetAnsID ($id_q,$answ);}
  }
  return $r;


}
function SkoPutInfo($par)
{
 $r='';
 $error="";
 $id=GetPostGetParamSTR("q");
 $id=str_replace("-","",$id);
 $place=GetPostGetParamINT("p");
 if ($place<1) {$error="Место не указанно";}
 $answ=GetPostGetParamSTR("a");
 if ($answ=="") {$error="Ответ не верен";}
 $id_q=GetPostGetParamINT("idq");
 if ($id_q<1) {$error="Ответ не верен!";}
 $us=GetPostGetParamSTR("us");
 $ps=GetPostGetParamSTR("ps");

 $sql='select sko_quests.id, sko_quests.id_user from sko_quests
 inner join auth_users on auth_users.id=sko_quests.id_user
 where (UNIX_TIMESTAMP(sko_quests.cod)='.$id.' and (auth_users.username="'.$us.'" or auth_users.mail="'.$us.'") and auth_users.pass="'.$ps.'")';
 $R = mysql_query_my ($sql) or $error="Internal server error".mysql_error();
 $T=mysql_fetch_array($R);
 if (is_array($T)&&$error=='')
 { 	$id_user=$T["id_user"];
 	$id_answ=GetAnswerID ($id_q,$answ);
    $sql='insert into sko_main (id_quest,id_answ,id_place) values ("'.$id_q.'", '.$id_answ.', '.$place.')';
    $R = mysql_query_my ($sql) or $e='err';
 }


 return $r;
}

?>