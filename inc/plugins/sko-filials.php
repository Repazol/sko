<?php

function FilialPost()
{
  global $page;
  $idu=$page['user']['id'];
  $r='';  $id=GetPostGetParamINT('id');
  $n=GetPostGetParamSTR('name');
  if ($n!="")
  {
  if ($id==-1)
  {
   $sql='insert into sko_filials (id_user,name) values('.$idu.',"'.$n.'")';
   $R = mysql_query_my ($sql) or $r.="Error in FilialPost<br>".mysql_error();
  } else
    {      $sql='update sko_filials set name="'.$n.'" where (id='.$id.' and id_user='.$idu.')';
      $R = mysql_query_my ($sql) or $r.="Error in FilialPost<br>".mysql_error();
    }

  } else {$r.='Название точки, не может быть пустым';}
  return $r;
}
function FilialDel()
{  global $page;
  $idu=$page['user']['id'];
  $r='';
  $id=GetPostGetParamINT('id');
  $n=GetPostGetParamSTR('name');
  $r.='<div id="dialog" title="Удалить точку опроса"><p>Удалить "'.$n.'".';
  $r.='<form action="" method="post"><input name="docomment" type="hidden" value="filial-del1"><input name="id" type="hidden" value="'.$id.'"><input name="name" type="hidden" value="'.$n.'"><input type="submit" value="Удалить">';
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
function FilialDel1()
{  global $page;
  $idu=$page['user']['id'];
  $r='';
  $id=GetPostGetParamINT('id');
  $sql='update sko_filials set is_del="Y" where (id='.$id.' and id_user='.$idu.')';
  $R = mysql_query_my ($sql) or $r.="Error in FilialDel1<br>".mysql_error();
  return $r;
}
function SkoFilials($par)
{
  global $page;
  $idu=$page['user']['id'];
  $do=GetPostGetParamSTR('docomment');
  $r='';
  if ($do=="filial-post") {$r.=FilialPost();}
  if ($do=="filial-del") {$r.=FilialDel();}
  if ($do=="filial-del1") {$r.=FilialDel1();}

  $r.='<h2>Мои точки опросов</h2>';
  $n=0;
  $r.='<table class="tbl_list"><tr><th>#</th><th>Название</th><th></th><th></th></tr>';
  $sql='select id,name from sko_filials where (id_user='.$idu.' and is_del="N") order by name';
  $R = mysql_query_my ($sql) or die ("Error in SkoFilials<br>".mysql_error());
  $T=mysql_fetch_array($R);
    while (is_array($T))
    {
      $n++;
      $r.='<tr><td>'.$n.'</td><form action="" method="post"><input name="docomment" type="hidden" value="filial-post"><input name="id" type="hidden" value="'.$T['id'].'"><td><input class="ta_quest" name="name" type="text" style="height:auto;" value="'.htmlspecialchars($T['name']).'"></td><td><input type="submit" value="Сохранить"></form></td>';
      $r.='<td><form action="" method="post"><input name="docomment" type="hidden" value="filial-del"><input name="id" type="hidden" value="'.$T['id'].'"><input name="name" type="hidden" value="'.$T['name'].'"><input type="submit" value="Удалить"></form></td></tr>';
 	  $T=mysql_fetch_array($R);
    }
  $r.='<tr><td></td><td><form action="" method="post"><input name="docomment" type="hidden" value="filial-post"><input name="id" type="hidden" value="-1"><input class="tx_quest" name="name" type="text" value=""><input type="submit" value="Создать"></form></td></tr>';
  $r.='</table>';

  return $r;
}

?>