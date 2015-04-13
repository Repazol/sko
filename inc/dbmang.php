<?php

function GetTableList()
{  $r='<b>Список таблиц</b>';
  $r.='<table border="1">';
  $r.='<tr><th>Таблица</th><th>Реальная</th></tr>';
  $R = mysql_query ('select tablename, name from databaseinfo') or die ("Error in GetTableList<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
    $r.='<tr><td><a href="index.php?do=dbedit&id='.$T['tablename'].'">'.$T['name'].'</a></td><td>'.$T['tablename'].'</td></tr>';
    $T=mysql_fetch_array($R);
  }

 $r.='</table>';
 return $r;
}
function doTableData($d)
{
  $rz=array();  $f=explode('#',$d);
  foreach($f as $key=>$x)
  {    $c=explode('@',$x);
    $fl=array();
    $fn='';
    foreach($c as $k=>$v)
    {      $dd=explode('=',$v);
      if ($dd[0]=='field') {$fn=$dd[1];}
      if ($dd[0]!='') {$fl[$dd[0]]=$dd[1];}
    }
    $rz[$fn]=$fl;
  }
/*  echo "<pre>";
  print_r($rz);
  echo "</pre>"; */
  return $rz;
}
function ShowTable()
{  global $id;
  include_once('engine.php');
  $R = mysql_query ('select name,tabledata, sql_show from databaseinfo where (`tablename`="'.$id.'")') or die ("Error in ShowTable<br>".mysql_error());
  $T=mysql_fetch_array($R);
  $fl=doTableData($T['tabledata']);
  $r='<b>Таблица '.$T['name'].'</b> <a href="index.php?do=dbedit-rec&id=-1&t='.$id.'">Добавить запись</a>';
  $r.='<table class="sortable" border="1">';
  //Применяем фильтры
  //$SqlWithFilter=$T['sql_show'];
  //
  $R = mysql_query ($T['sql_show']) or die ("Error in ShowTable SQL<br>".mysql_error());
  $T=mysql_fetch_array($R);
  $n=0;
  $filters=array();
  $db='<pre>';
  while (is_array($T))
  {
    $n++;
    $l='<a href="index.php?do=dbedit-rec&id='.$T['id'].'&t='.$id.'">';
    if ($n==1) { //Заголовок Таблицы       $r.='<thead><tr><th width="20">№</th>';
       foreach($T as $k=>$v)
       if (isset($fl[$k]))
         {           $r.='<th>'.$fl[$k]['title'].'</th>';
           $filters[$k]='';//$fl[$k]['atype'];
           $filters_v[$k]=GetPostGetParam('fil_'.$fl[$k]['field']);//$fl[$k]['atype'];
           $filters_vl[$k]=array();
           if ($fl[$k]['atype']=='INPUT') {$filters[$k]='<input name="fil_'.$fl[$k]['field'].'" type="text" value="'.$filters_v[$k].'" style="margin:0;">';}
           if ($fl[$k]['atype']=='COMBOBOX') {$filters[$k]='<select size="1" name="fil_'.$fl[$k]['field'].'" style="margin:0;"><option value=""> Не фильтровать</option>';};
         }
       $r.='</tr><!--FILTERS--></thead>';
    }
    $rec_ok=true;
    $rr='<tr><td><b>'.$l.$n.'</a></td>';
    $nnn=0;
    foreach($T as $k=>$v)
    if (isset($fl[$k]))
      {
       $g=GetPostGetParam('fil_'.$fl[$k]['field']);
       $db.='k='.$fl[$k]['field'].' g='.$g.'('.strlen($g).') ';
       if ($g!='')
       {
       	 if (mb_strpos($v,$g,0,'UTF-8')===false) {$rec_ok=false;$db.='>'.$v.' '.$g.chr(13);}
       }
       if ($nnn==0) {$titt=$v;}
       $nnn=1;
       if ($fl[$k]['atype']=='IMG') {$v='<img src="'.$v.'" width="50">';}
       $rr.='<td>'.$l.$v.'</a></td>';
       if ($fl[$k]['atype']=='COMBOBOX'&&!in_array($v, $filters_vl[$k]))
          {
             $filters_vl[$k][]=$v;          	 $filters[$k].='<option value="'.$v.'"';
          	 if ($filters_v[$k]==$v) {$filters[$k].=' selected';}
          	 $filters[$k].='>'.$v.'</option>';};
      }
    $rr.='<td><a href="index.php?do=del-dbrec&id='.$T['id'].'&t='.$id.'&tit='.$titt.'">X</a></td></tr>';
    if ($rec_ok) {$r.=$rr;}
    $T=mysql_fetch_array($R);
  }
 $filt='<tr><td></td><form name="" action="index.php" method="get">';
 $filt.='<input name="do" type="hidden" value="dbedit">';
 $filt.='<input name="id" type="hidden" value="'.$id.'">';
 foreach($filters as $k=>$v) {$filt.='<td>'.$v.'</td>';}
 $filt.='<td><input type="submit" value="Фильтр" class="small green" style="padding: 1px 5px;"></form></td></tr>';
 $r.='</table>';
 $r=str_replace('<!--FILTERS-->',$filt,$r);
 $db.='</pre>';
 //$r.=$db;
 return $r;
}
function GenerateCombo($v, $s, $val)
{
  $v['add']=str_replace('~','=',$v['add']);  $r='<select size="1" name="f_'.$v['field'].'" '.$s.'>';
  $R = mysql_query ($v['add']) or die ("Error in GenerateCombo SQL<br>".mysql_error().'<br><pre>'.print_r($v, true).'</pre>');
  $T=mysql_fetch_array($R);
  $r.='<option value="-1">Не выбранно</option>';
  while (is_array($T))
  {
    $ss='';
    if ($T['id']==$val) {$ss='selected';}
    $r.='<option value="'.$T['id'].'" '.$ss.'>'.$T['name'].'</option>';
    $T=mysql_fetch_array($R);
  }

  $r.='</select>';
  return $r;
}
function GenerateCheckBoxes($id, $chec_box_p)
{
  $ch=array();
  $sq='select '.$chec_box_p[4].' as i from '.$chec_box_p[2].' where ('.$chec_box_p[3].'='.$id.')';
  $R = mysql_query ($sq) or die ("Error in GenerateCheckBoxes SQL<br>".mysql_error().'<br><pre>'.print_r($chec_box_p, true).'</pre>');
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
    $ch[$T['i']]=true;
    $T=mysql_fetch_array($R);
  }
  $r='<b>'.$chec_box_p[1].'</b><br>';
  $sq=$chec_box_p[5];
  $R = mysql_query ($sq) or die ("Error in GenerateCheckBoxes SQL<br>".mysql_error().'<br><pre>'.print_r($chec_box_p, true).'</pre>');
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {  	if (isset($ch[$T['id']])) {$s=' checked';} else {$s='';}
    $r.='<input name="ch_'.$T['id'].'" type="checkbox"'.$s.'> '.$T['name'].'<br>';
    $T=mysql_fetch_array($R);
  }
  return $r;
}
function EditRecord()
{  global $id, $head_html;
  if ($id=='') {$id=-1;}
  include_once('engine.php');
  $t=GetPostGetParam('t');
  $R = mysql_query ('select * from '.$t.' where (id='.$id.')') or die ("Error in EditRecord<br>".mysql_error());
  $VL=mysql_fetch_array($R);

  $R = mysql_query ('select name,tabledata, additional from databaseinfo where (`tablename`="'.$t.'")') or die ("Error in EditRecord<br>".mysql_error());
  $T=mysql_fetch_array($R);
  $fl=doTableData($T['tabledata']);
  $additional=explode('#',$T['additional']);
  $chec_box=false;
  foreach($additional as $k=>$v)
  {    $p=explode('>',$v);
    if ($p[0]=='CHECKBOXSES')
      {        $chec_box=true;
        $chec_box_p=$p;
      }
  }

  $r='<b>Редактируем запись в таблице:<a href="index.php?do=dbedit&id='.$t.'">'.$T['name'].'</a></b><br>';
  $r.='<form action="index.php" method="POST">';
  if ($chec_box) {$r.='<table width="100%"><tr><td valign="top" width="*">';}
  $r.='<input name="do" type="hidden" value="dbedit-rec-post">';
  $r.='<input name="id" type="hidden" value="'.$id.'">';
  $r.='<input name="table" type="hidden" value="'.$t.'">';
  $r.='<table width="100%">';
  $rich=false;
  foreach($fl as $k=>$v)
  if ($k!='')
  {
    $f='';$val='';
    if (isset($VL[$v['field']]))
    {       $val=$VL[$v['field']];//2013-04-12    17-04-2013
       if ($v['type']=='DATETIME') {$val=substr($val,8,2).'-'.substr($val,5,2).'-'.substr($val,0,4);};
    } else {if ($v['field']=='pos') {$val='9999';}}
    $s='style="width:'.$v['width'].';height:'.$v['height'].';display:inline;margin:0;padding:0;"';
    if (!isset($v['atype'])||$v['atype']=='') {$v['atype']='INPUT';}
    if ($v['atype']=='DATE') {$f='<input class="date" name="f_'.$k.'" type="text" value="'.$val.'" '.$s.'><br>';}
    if ($v['atype']=='COMBOBOX') {$f=GenerateCombo($v, $s, $val).'<br>';}
    if ($v['atype']=='INPUT') {$f='<input name="f_'.$k.'" type="text" value="'.$val.'" '.$s.'><br>';}
    if ($v['atype']=='MEMO') {$f='<textarea name="f_'.$k.'" '.$s.'>'.$val.'</textarea>';}
    if ($v['atype']=='RICHVIEW') {$rich=true;$f='<textarea id="editor1" class="ckeditor" name="f_'.$k.'" '.$s.'>'.$val.'</textarea>';}
    if ($v['atype']=='IMG') {$f.='<input '.$s.' type="text" id="srcFile_function" name="f_'.$k.'" value="'.$val.'">&nbsp;<input style="margin:0 0 0 0;display:inline;" type="button" value="Выбрать" onclick="AjexFileManager1.open({returnTo: \'insertValue\'});" >';}
    if ($f!='')
      {        $r.='<tr valign="top" align="right"><td width="100">'.$v['title'].':</td><td align="left">'.$f.'</td></tr>';
      } else $r.='<tr><td>'.$v['title'].'</td><td>'.print_r($v,true).'</td></tr>';
  }
  $r.='<tr><td></td><td><input type="submit" value="Сохранить"></td></tr>';
  $r.='</table>';
  if ($chec_box) {$r.='</td><td valign="top" style="'.$chec_box_p[6].'">'.GenerateCheckBoxes($id, $chec_box_p).'</td></tr></table>';}
  if ($rich) {$ingtml=file_get_contents('ckeditor/inhtml.dat');} else {$ingtml=file_get_contents('ckeditor/inhtml1.dat');}
  $r.=$ingtml;
  $head_html='<script type="text/javascript" src="ckeditor/ckeditor.js"></script><script type="text/javascript" src="AjexFileManager/ajex.js"></script><script type="text/javascript" src="AjexFileManager/ajex1.js"></script>';
  $head_html.='<link rel=\'stylesheet\' href=\'calendar/calendar.css\' type=\'text/css\'><script type=\'text/javascript\' src=\'calendar/calendar.js\'></script>';
  //$r.='<pre>'.print_r($fl,true).'</pre>';
  return $r;
}
function EditRecordPOST()
{  global $id;
  include_once('engine.php');
  $t=GetPostGetParam('table');
  $R=mysql_query ('select name,tabledata,additional from databaseinfo where (`tablename`="'.$t.'")') or die ("Error in EditRecordPOST<br>".mysql_error());
  $T=mysql_fetch_array($R);
  $fl=doTableData($T['tabledata']);
  $additional=explode('#',$T['additional']);
  foreach($additional as $k=>$v)
  {
    $p=explode('>',$v);
    if ($p[0]=='CHECKBOXSES')
      {
        $chec_box=true;
        $chec_box_p=$p;
      }
  }
  $r='<div class="notice success"><i class="icon-ok icon-large"></i><b>Запись сохранена в таблице:'.$T['name'].'</b><a href="#close" class="icon-remove"></a></div>';
  $int_t=array('INT','BIGINT','FLOAT','DOUBLE');
  $sql1='';$sql2='';$sql3='';$tt='';
  foreach($_POST as $k=>$v)
  if (substr($k,0,2)=="f_")
  {
    $f=substr($k,2);
    $inss=in_array($fl[$f]['type'],$int_t);
    if ($inss) {$inss='I';} else {$inss='S';}
    if ($fl[$f]['type']=='DATETIME')
      {//2013-04-12      	$v=substr($v,6,4).'-'.substr($v,3,2).'-'.substr($v,0,2);
      }
    $tt.=$k.' '.$fl[$f]['type'].' ('.$inss.')<br>';
    $sql1.=','.$f;
    $v=mysql_real_escape_string($v);
    if ($inss=='S') {$sql2.=', "'.$v.'"';}
      else {$sql2.=', '.$v;}
    if ($inss=='S') {$sql3.=', '.$f.'="'.$v.'"';}
      else {$sql3.=', '.$f.'='.$v;}
  }
  $sql1=substr($sql1,1);
  $sql2=substr($sql2,1);
  $sql3=substr($sql3,1);
  if ($id==-1) {$sql='insert into `'.$t.'` ('.$sql1.') values ('.$sql2.')';}
    else {$sql='update `'.$t.'` set '.$sql3.' where (id='.$id.')';}
  //$r.='SQL :'.$sql;
  $R=mysql_query ($sql) or die ("Error in EditRecordPOST Exec<br>".mysql_error().'<br>'.$sql.'<br>id='.$id.'<br>t='.$tt);
  if ($chec_box) //Сохраняем чекбоксы
  {    if ($id==-1) //Узнаем id новой записи
    {       $R = mysql_query ('select LAST_INSERT_ID() as id from '.$t) or die ("Error in EditRecordPOST LIID<br>".mysql_error());
       $T=mysql_fetch_array($R);
       $id=$T['id'];
    }
    $R = mysql_query ('delete from '.$chec_box_p[2].' where ('.$chec_box_p[3].'='.$id.')') or die ("Error in EditRecordPOST DEL<br>".mysql_error());
    foreach($_POST as $k=>$v)
    if (substr($k,0,3)=="ch_")
       {
         $v=substr($k,3);         $R = mysql_query ('insert into '.$chec_box_p[2].' ('.$chec_box_p[3].','.$chec_box_p[4].') values ('.$id.','.$v.')') or die ("Error in EditRecordPOST INS<br>".mysql_error());
       }
    }

  $id=$t;
  return $r.ShowTable();
}
function DelDbRecord()
{  global $do_res, $id;
  include_once('engine.php');
  $t=GetPostGetParam('t');
  $tit=GetPostGetParam('tit');
  $r='<table width="100%" border=0>';
  $r.='<tr><th align="center" colspan="2"><b>Удалить запись?</b><br>'.$tit.'</th></tr>';
  $r.='<tr><td width="50%" align="right">';
  $r.='<form action="index.php" method="POST">';
  $r.='<input name="do" type="hidden" value="del-dbrec-ok">';
  $r.='<input name="t" type="hidden" value="'.$t.'">';
  $r.='<input name="id" type="hidden" value="'.$id.'">';
  $r.='<input type="submit" value="Удалить"></form></td>';
  $r.='<td><form action="index.php" method="GET"><input name="do" type="hidden" value="blog"><input type="submit" value="Нет"></form></td></tr></table>';
  return $r;
}
function DelDbRecordOK()
{  global $do_res, $id;
  include_once('engine.php');
  $t=GetPostGetParam('t');
  $r='<div class="notice error"><i class="icon-remove-sign icon-large"></i><b>Запись удаленна</b><a href="#close" class="icon-remove"></a></div>';
  $R = mysql_query ('delete from '.$t.' where (id='.$id.')') or die ("Error in DelDbRecordOK<br>".mysql_error());
  $id=$t;
  return $r.ShowTable();
}
function DBManager()
{
  global $do_res, $id;
  $r='';

  if ($do_res=='dbedit') {$r=ShowTable();}
  if ($do_res=='dbedit-rec') {$r=EditRecord();}
  if ($do_res=='dbedit-rec-post') {$r=EditRecordPOST();}
  if ($do_res=='del-dbrec') {$r=DelDbRecord();}
  if ($do_res=='del-dbrec-ok') {$r=DelDbRecordOK();}




  if ($r=='') {
     $r=GetTableList();
  }
  return $r;
};



?>