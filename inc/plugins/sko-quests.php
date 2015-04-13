<?php

function SkoQPost()
{
  global $page;
  $idu=$page['user']['id'];
  $r='';
  $id=GetPostGetParamINT('id');
  $n=GetPostGetParamSTR('name');
  if ($n!="")
  {
  if ($id==-1)
  {
   $sql='insert into sko_quests (id_user,name) values('.$idu.',"'.$n.'")';
   $R = mysql_query_my ($sql) or $r.="Error in SkoPost<br>".mysql_error();
  } else
    {
      $sql='update sko_quests set name="'.$n.'" where (id='.$id.' and id_user='.$idu.')';
      $R = mysql_query_my ($sql) or $r.="Error in SkoPost<br>".mysql_error();
    }

  } else {$r.='Название точки, не может быть пустым';}
  return $r;
}
function SkoQDel()
{
  global $page;
  $idu=$page['user']['id'];
  $r='';
  $id=GetPostGetParamINT('id');
  $n=GetPostGetParamSTR('name');
  $r.='<div id="dialog" title="Удалить опрос"><p>Удалить "'.$n.'".';
  $r.='<form action="" method="post"><input name="docomment" type="hidden" value="quest-del1"><input name="id" type="hidden" value="'.$id.'"><input name="name" type="hidden" value="'.$n.'"><input type="submit" value="Удалить">';
  $r.='</p></div>';
  $r.='<script>
  $(function() %[%
    $( "#dialog" ).dialog(%[%
      height: 160,
      width: 450,
      modal: true,
    %]%);
  %]%);
  </script>';
  return $r;
}
function SkoQDel1()
{
  global $page;
  $idu=$page['user']['id'];
  $r='';
  $id=GetPostGetParamINT('id');
  $sql='update sko_quests set is_del="Y" where (id='.$id.' and id_user='.$idu.')';
  $R = mysql_query_my ($sql) or $r.="Error in FilialDel1<br>".mysql_error();
  return $r;
}
function SkoQuests($par)
{  global $page;
  $idu=$page['user']['id'];
  $r='';
  $do=GetPostGetParamSTR('docomment');
  if ($do=="quest-post") {$r.=SkoQPost();}
  if ($do=="quest-del") {$r.=SkoQDel();}
  if ($do=="quest-del1") {$r.=SkoQDel1();}

  $r.='<h2>Мои опросы</h2>';

  $n=0;
  $r.='<table class="tbl_list"><tr><th>#</th><th style="width:120px;">Код</th><th>Название</th><th></th><th></th><th></th><th>Изменен</th></tr>';
  $sql='select id,name,UNIX_TIMESTAMP(cod) as cod, `change` from sko_quests where (id_user='.$idu.' and is_del="N") order by name';
  $R = mysql_query_my ($sql) or die ("Error in SkoFilials<br>".mysql_error());
  $T=mysql_fetch_array($R);
    while (is_array($T))
    {
      $n++;
      $cod=substr($T['cod'],0,3).'-'.substr($T['cod'],3,3).'-'.substr($T['cod'],6);
      $r.='<tr><td style="text-align:center;"><b>'.$n.'</b></td><form action="" method="post"><input name="docomment" type="hidden" value="quest-post"><input name="id" type="hidden" value="'.$T['id'].'">';
      $r.='<td style="text-align:center;"><b>'.$cod.'</b></td>';
      $r.='<td><input style="height:auto;" class="ta_quest" name="name" type="text" value="'.$T['name'].'"></td><td><input type="submit" value="Сохранить"></form></td>';
      $r.='<td><form action="questions_'.$T['id'].'"><input type="submit" value="Вопросы"></form></td>';
      $r.='<td><form action="" method="post"><input name="docomment" type="hidden" value="quest-del"><input name="id" type="hidden" value="'.$T['id'].'"><input name="name" type="hidden" value="'.$T['name'].'"><input type="submit" value="Удалить"></form></td>';

      $r.='<td>'.$T['change'].'</td></tr>';
 	  $T=mysql_fetch_array($R);
    }
  $r.='<tr><td></td><td></td><td><form action="" method="post"><input name="docomment" type="hidden" value="quest-post"><input name="id" type="hidden" value="-1"><input class="tx_quest" name="name" type="text" value=""><input type="submit" value="Создать"></form></td></tr>';
  $r.='</table>';

  return $r;
}

?>