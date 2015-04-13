<?php
function QuestionPost()
{
  global $page;
  $idu=$page['user']['id'];
  $r='';
  $id=GetPostGetParamINT('id');
  $idq=GetPostGetParamINT('idq');
  $n=GetPostGetParamSTR('name');
  $a=GetPostGetParamSTR('answ');
  $qd=GetPostGetParamSTR('condition');
  $pos=GetPostGetParamINT('pos');
  if ($n!="")
  {
  if ($id==-1)
  {
   $sql='select max(pos) as mp from sko_questions where (id_user='.$idu.' and id_q='.$idq.')';
   $R = mysql_query_my ($sql) or $r.="Error in QuestionPost<br>".mysql_error();
   $T=mysql_fetch_array($R);
   if (is_array($T)) {$newpos=$T['mp']+100;} else {$newpos=100;}
   $sql='insert into sko_questions (id_user,question,id_q,pos) values('.$idu.',"'.$n.'",'.$idq.','.$newpos.')';
   $R = mysql_query_my ($sql) or $r.="Error in QuestionPost<br>".mysql_error();
  } else
    {
      $sql='update sko_questions set question="'.$n.'", answers="'.$a.'", pos='.$pos.',`condition`="'.$qd.'" where (id='.$id.' and id_user='.$idu.')';
      $R = mysql_query_my ($sql) or $r.="Error in QuestionPost<br>".mysql_error();
    }

    $sql='update sko_quests set `change`=NOW() where (id='.$idq.' and id_user='.$idu.')';
    $R = mysql_query_my ($sql) or $r.="Error in QuestionPost<br>".mysql_error();

  } else {$r.='<div class="alert-box error"><span>ошибка: </span>Вопрос, не может быть пустым.</div>';}
  return $r;
}
function QuestionDel()
{
  global $page;
  $idu=$page['user']['id'];
  $r='';
  $id=GetPostGetParamINT('id');
  $n=GetPostGetParamSTR('name');
  $r.='<div id="dialog" title="Удалить вопрос"><p>"'.$n.'"?';
  $r.='<form action="" method="post"><input name="docomment" type="hidden" value="question-del1"><input name="id" type="hidden" value="'.$id.'"><input type="submit" value="Удалить">';
  $r.='</p></div>';
  $r.='<script>
  $(function() %[%
    $( "#dialog" ).dialog(%[%
      width: 450,
      modal: true,
    %]%);
  %]%);
  </script>';
  return $r;
}
function QuestionDel1()
{
  global $page;
  $idu=$page['user']['id'];
  $r='';
  $id=GetPostGetParamINT('id');
  $sql='update sko_questions set is_del="Y" where (id='.$id.' and id_user='.$idu.')';
  $R = mysql_query_my ($sql) or $r.="Error in FilialDel1<br>".mysql_error();
  return $r;
}


function SkoQuestions($par)
{  global $page;
  $id_q=$page['vi1'];
  if ($id_q=="") {$id_q="-1";}
  $idu=$page['user']['id'];
  $do=GetPostGetParamSTR('docomment');
  $r='';
  if ($do=="question-post") {$r.=QuestionPost();}
  if ($do=="question-del") {$r.=QuestionDel();}
  if ($do=="question-del1") {$r.=QuestionDel1();}

  $sql='select name,is_del,UNIX_TIMESTAMP(cod) as cod from sko_quests where (id='.$id_q.' and id_user='.$idu.')';
  $R = mysql_query_my ($sql) or die ("Error in SkoQuestions<br>".mysql_error());
  $T=mysql_fetch_array($R);
  if ($T['is_del']=="Y") {$d='[удален]';} else {$d='';}
  $qu=$T['name'].$d;
  $cod=substr($T['cod'],0,3).'-'.substr($T['cod'],3,3).'-'.substr($T['cod'],6);
  $qu.=' '.$cod;

  $r.='<h2>Вопросы ('.$qu.')</h2>';
  $n=0;
  $r.='<table class="tbl_list"><tr><th>#</th><th>Вопрос</th><th>Ответы</th><th>Сортировка</th><th>Условие<sup>?</sup></th><th></th><th></th><th></th></tr>';
  $sql='select id,question,answers,`condition`,pos from sko_questions where (id_user='.$idu.' and id_q='.$id_q.' and is_del="N") order by pos';
  $R = mysql_query_my ($sql) or die ("Error in SkoQuestions<br>".mysql_error());
  $T=mysql_fetch_array($R);
    while (is_array($T))
    {
      $n++;
      $r.='<tr><td>'.$n.'</td><form action="" method="post"><input name="docomment" type="hidden" value="question-post"><input name="id" type="hidden" value="'.$T['id'].'">';
      $r.='<td><textarea name="name" class="ta_quest">'.$T['question'].'</textarea></td>';
	  $r.='<td><textarea name="answ" class="ta_quest">'.$T['answers'].'</textarea></td>';
	  $r.='<td><input name="pos" type="text" value="'.$T['pos'].'"></td>';
	  $r.='<td><textarea name="condition" class="ta_quest">'.$T['condition'].'</textarea></td>';
      $r.='<td><input name="idq" type="hidden" value="'.$id_q.'"><input type="submit" value="Сохранить"></form></td>';
      $r.='<td><form action="design_'.$T['id'].'"><input type="submit" value="Дизайн"></form></td>';
      $r.='<td><form action="" method="post"><input name="docomment" type="hidden" value="question-del"><input name="id" type="hidden" value="'.$T['id'].'"><input name="name" type="hidden" value="'.$T['question'].'"><input type="submit" value="Удалить"></form></td></tr>';
 	  $T=mysql_fetch_array($R);
    }
  $r.='<tr><td></td><td><form action="" method="post"><input name="docomment" type="hidden" value="question-post"><input name="id" type="hidden" value="-1"><input name="idq" type="hidden" value="'.$id_q.'"><input class="tx_quest" name="name" type="text" value=""><input type="submit" value="Создать"></form></td></tr>';
  $r.='</table>';


  return $r;
}

?>