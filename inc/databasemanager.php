<?php

function GetTableList()
{ $r='<b>Управление данными<b><br>';
 $r.='<table class="striped tight sortable">';
 $r.='<tr><th>Код</th><th>Таблица</th><th>Категория</th></tr>';
 $R = mysql_query ('select id, tname, cat, pos from tabels order by pos, cat, tname') or die ("Error in GetTableList<br>".mysql_error());
 $T=mysql_fetch_array($R);
 while (is_array($T))
  {
     $l='<a href="index.php?do=mysql-table&id='.$T['id'].'">';
     $r.='<tr><td>'.$l.'t'.$T['id'].'</a></td><td>'.$T['tname'].'</td><td>'.$T['cat'].'</td></tr>';
 	 $T=mysql_fetch_array($R);
  }

 $r.='</table>';
 return $r;
}

function GetParam ($par, $def)
{
 if (isset($_POST[$par])) {$r=mysql_real_escape_string($_POST[$par]);}
      else if (isset($_GET[$par])) {$r=mysql_real_escape_string($_GET[$par]);}
        else {$r=$def;}
 return $r;
}
function GetTableDefs($id_table)
{ $R = mysql_query ('select id, tname, cat, filds, def, view from tabels where (id='.$id_table.')') or die ("Error in GetTableDefs<br>".mysql_error());
 $T=mysql_fetch_array($R);
 $r=$T;
 $fil=explode(chr(13).chr(10),$r['filds']);
 $fl_t=array();
 $fl_o=array();
 foreach($fil as $key=>$x)
 {
   $dt=explode ('@',$x);
   if (count($dt)>1) {$fl[$dt[1]]=$dt[0];$fl_t[$dt[1]]=$dt[2];if (substr($dt[2],0,1)=='o') {$ido=substr($dt[2],1)+0;$fl_o[$dt[1]]=array();$fl_o[$dt[1]]['id']=$ido;$fl_o[$dt[1]]['fl']=$dt[1];};}
 }
 $r['fl']=$fl;
 $r['fl_t']=$fl_t;
 // Calculate Default
 $def_f='id';
 foreach($fl as $pl=>$nm)
    {
      $e=strpos(' '.$r['def'],$nm);
      if ($e>0) {$def_f.=','.$pl;}
      //echo $r['def']." @$nm@ $pl -$e- $def_f<br>";
    }
 $r['def_f']=$def_f;
 // Calculate object fields
 foreach($fl_o as $key=>$x)
 {
    $tb_o=GetTableDefs($x['id']);
    $fl_o[$key]['data']=$tb_o;
    $fl_o[$key]['value']=array();
    $R = mysql_query ('select '.$tb_o['def_f'].' from t'.$tb_o['id']) or die ("Error in ShowTable Obj<br>".mysql_error());
    $T=mysql_fetch_array($R);
    while (is_array($T))
    {
      $d=$tb_o['def'];
      foreach($T as $key1=>$x1)
      {      	if (isset($tb_o['fl'][$key1]))
      	  {      	    $pl=$tb_o['fl'][$key1];
      	    $d=str_replace('<'.$pl.'>',$x1,$d);
      	  };
      }      $fl_o[$key]['value'][$T['id']]=$d;
      //echo "key=$key<br>";
      $T=mysql_fetch_array($R);
    }
 }
 //
 $r['fl_o']=$fl_o;
 return $r;
}

function ShowTable($id)
{
 $tb=GetTableDefs($id);
 $fl=$tb['fl'];
 $fl_t=$tb['fl_t'];
 $fl_o=$tb['fl_o'];

 //
 $r='<center><b>'.$tb['tname'].' ('.$tb['cat'].')</b></center>';
 $id_p=GetParam('id_p','-1');
 $R = mysql_query ('select id, name, data from profiles where (id_table='.$id.') order by name') or die ("Error in ShowTable<br>".mysql_error());
 $T=mysql_fetch_array($R);
 $fields='';$dfields='';
 $r.='<div style="position: absolute; display:block;width:200px;right:0;">Профиль:<select size="1" name="id_p">';
 while (is_array($T))
  {
     if ($T['id']==$id_p) {$fields=$T['data'];$s=' selected';} else {$s='';}
     if ($T['name']=='По умолчанию') {$dfields=$T['data'];}
     $r.='<option value="'.$T['id'].'">'.$T['name'].'</option>';
 	 $T=mysql_fetch_array($R);
  }
  $r.='</select></div><br>';
  if ($fields=='') {$fields=$dfields;}
  //$r.=$fields.'<br>fl:'.print_r($fl,true).'<br>fl_t:'.print_r($fl_t,true).'<br>fl_o:'.print_r($fl_o, true).'<hr>'.print_r($tb, true).'<hr>';

  $pl=explode (',',$fields);
  $r.='<table class="striped tight sortable"><tr><th>ID</th>';
  $f='id';
  foreach($pl as $key=>$x) {if ($x!='') {$r.='<th>'.$fl[$x].'</th>';$f.=','.$x;}}
  $sel=explode (',',$f);
  $r.='</tr>';
  $R = mysql_query ('select '.$f.' from t'.$id) or die ("Error in ShowTable<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
     $link='<a href="index.php?do=mysql-edit&id='.$T['id'].'&id_t='.$id.'">';
     $r.='<tr>';
     foreach($sel as $key=>$x)
     {     	$d=$T[$x];
     	if (isset($fl_o[$x])) {if (isset($fl_o[$x]['value'][$d])) {$d=$fl_o[$x]['value'][$d];} else {$d='';}}
     	$r.='<td>'.$link.$d.'</a></td>';
     }
     $r.='</tr>';
 	 $T=mysql_fetch_array($R);
  }

  $r.='</table>';
  return $r;
}
function EditRecord($id)
{
  $id_t=GetParam('id_t','-1');
  if ($id_t!=-1)
  {    $tb=GetTableDefs($id_t);
    foreach($tb['fl'] as $key=>$x) {$T[$x]='';}
    if ($id!=-1)
    {      $R = mysql_query ('select * from t'.$id_t.' where (id='.$id.')') or die ("Error in ShowTable<br>".mysql_error());
      $T=mysql_fetch_array($R);
    }
    $r='<center><b>'.$tb['tname'].' ('.$tb['cat'].') - Редактирование</b></center><br>';
    $r.='<form name="" action="index.php" method="post">';
    $r.='<input name="id" type="hidden" value="'.$id.'">';
    $r.='<input name="id_t" type="hidden" value="'.$id_t.'">';
    $r.='<input name="mysql-post" type="hidden" value="">';
    $v=explode(chr(13).chr(10),$tb['view']);
    foreach($v as $key=>$x)
    {      $d=explode('@',$x);
      if (isset($d[2])&&$d[2]=='3')
      {        $pl=array_search($d[3], $tb['fl']);
        $t=substr($tb['fl_t'][$pl],0,1);
        $htm='Не поддерживается:'.$pl;
        if ($t=='c'||$t=='n') {$htm='<input style="margin:0 0 0 0;" name="rec_t_'.$pl.'" type="text" value="'.$T[$pl].'">';}
        if ($t=='t') {$htm='<textarea style="margin:0 0 0 0;height:100px;" name="rec_t_'.$pl.'" rows=5 cols=20 wrap="off">'.$T[$pl].'</textarea>';}
        if ($t=='o')
        {        	$htm='<select size="1" style="margin:0 0 0 0;" name="rec_n_'.$pl.'">';
        	foreach($tb['fl_o'][$pl]['value'] as $k=>$y)
        	  {
        	     if ($T[$pl]==$k) {$s=' selected';} else {$s='';}        	  	 $htm.='<option value="'.$k.'"'.$s.'>'.$y.'</option>';
        	  }
        	$htm.='</select><br>';
        }
        if ($t=='b')
         {         	if ($T[$pl]=="Y") {$s=' checked';} else {$s='';}         	$htm='<input style="margin:0 0 0 0;" name="rec_b_'.$pl.'" type="checkbox"'.$s.'>';
         }
        $r.='<b>'.$d[3].':</b><br>'.$htm.'<br>';
      }
    }
    $r.='<br><input type="submit" value="Сохранить"></form>';
  } else {$r='<b>Ошибка</b><br>';}
  return $r;
}
function DatabaseManager()
{
  global $do_res, $id;  $r='';

  if ($do_res=='mysql-table') {$r=ShowTable($id);}
  if ($do_res=='mysql-edit') {$r=EditRecord($id);}

  if ($r=='') {
     $r=GetTableList();
  }
  return $r;
};

?>