<?php

function SkoGetTample($id, &$name, &$a_orintation)
{ $r=''; $sql='select id, name, html,aorintation from sko_tmpls where (id='.$id.')';
 $R = mysql_query_my ($sql) or die ("Error in GenereateDesign<br>".mysql_error());
 $T1=mysql_fetch_array($R);
 if (is_array($T1))
    {
      $r=$T1['html'];
      $name=$T1['name'];
      $a_orintation=$T1['aorintation'];
    }
 return $r;
}

function GenereateDesign($id_q,$idu)
{
  $tmplname='';$a_orintation='';  $sql='select * from sko_questions where (id='.$id_q.' and id_user='.$idu.')';
  $R = mysql_query_my ($sql) or die ("Error in GenereateDesign<br>".mysql_error());
  $T=mysql_fetch_array($R);
  $id_tmpl=$T['tmpl'];
  $tmpl=SkoGetTample($id_tmpl,$tmplname,$a_orintation);
  $qst='<div id="offset1" style="display:block;"></div><div id="quest" class="quest">'.$T['question'].'</div>';
  $tmpl=str_replace('%QUESTION%',$qst,$tmpl);
  $answ=explode(chr(13),$T['answers']);
  $anshtml='<div id="offset2" style="display:block;"></div><table style="width:100%;border-spacing:5 20px;">';
  $acells='';
  $atextal='text-align:left;';
  if ($a_orintation=="H")
  {  	$atextal='text-align:center;';
    $wp=98/(count($answ)+1);
    $atextal.='width:'.$wp.'%;';
  }

  $n=0;
  foreach($answ as $key => $ans)
  {
    $cell='<td style="'.$atextal.'"><button id="qbth'.$n.'" class="qbth" style="width:98%;text-shadow: #000 0px 0px 2px;">'.$ans.'</button></td>';
    if ($a_orintation=="H") {$acells.=$cell;}
    if ($a_orintation=="V") {$acells.='<tr>'.$cell.'</tr>';}
    $n++;
  }
  $anshtml.=$acells.'</table>';
  $tmpl=str_replace('%ANSWERS%',$anshtml,$tmpl);

  return $tmpl;
}
function SKoGenerateFont($font,$name)
{
  $r='';
  $fonts=array('Arial','Courer','Times New Roman','Tahoma');
  $fn='Arial';$sz=14;$color="#000000";
  $fff=explode ('|',$font);
  if (count($fff)==3)
  {  	$fff[]='#000000';  	$fff[]='0';
  }
  if (count($fff)==5)
   {   	 $fn=$fff[0];
   	 $sz=$fff[1];
   	 $color=$fff[2];
   	 $sh_color=$fff[3];
   	 $sh_size=$fff[4];

   }

  $r.='<select id="'.$name.'_font" class="fontsel" size="1" name="'.$name.'_font">';
  foreach($fonts as $key =>$f)
  {
     if ($f==$fn) {$s=' selected';} else {$s='';}
     $r.='<option value="'.$f.'"'.$s.'>'.$f.'</option>';
  }
  $r.='</select> ';
  $r.='<input class="spinner" id="'.$name.'_size" name="'.$name.'_size" value="'.$sz.'"> ';
  $r.='<input name="'.$name.'_color" id="cp_'.$name.'" value="'.$color.'" class="cpick" style="width:60px;"/></br> ';
  //shadow
  $r.='<table><tr>';
  $r.='<td><input name="'.$name.'_shcolor" id="cps_'.$name.'" value="'.$sh_color.'" class="cpick" style="width:60px;"/></td> ';
  $r.='<td><input class="spinner" id="'.$name.'_shsize" name="'.$name.'_shsize" value="'.$sh_size.'"></td> ';
  $r.='</tr></table>';

  return $r;
}
function SkoGenerateOffs($x,$y,$n)
{  $r="\n".'<b>X:</b><input class="spinner" name="'.$n.'_offsx" id="'.$n.'_offsx" type="text" value="'.$x.'">';
  $r.=' <b>Y:</b><input class="spinner" name="'.$n.'_offsy" id="'.$n.'_offsy" type="text" value="'.$y.'">'."\n";
  return $r;
}
function SkoGenerateBGImage($bgimage, $btns=false)
{  global $page;
  $p='';
  if ($btns) {$p='b';}
  $page['vars']['bgsel'.$p]="\n".'<input style="width:220px; margin:0 0 0 0;display:inline;" type="text" id="srcFile_function'.$p.'" name="srcFile_function'.$p.'" value="'.$bgimage.'" size="50" />&nbsp;';
  $page['vars']['bgsel'.$p].='<input style="margin:0 0 0 0;display:inline;" type="button" value="Выбрать" onclick="AjexFileManager1.open({returnTo: \'insertValue'.$p.'\'});" />&nbsp;
  <input style="margin:0 0 0 0;display:inline;" type="button" value="Без фона" onclick="SetCanvaseBG'.$p.'(\'\');" /><br>'."\n";

  $r='%%bgsel'.$p.'%%';

  return $r;
}
function ButtonsImageSelect($answs, $btnimages)
{
  global $page;  $r='<table width="100%">';
  $answ=explode(chr(13),$answs);
  $bi=explode('|',$btnimages);
  $n=0;
  foreach($answ as $key => $ans)
  {
    $im='';
    if (isset($bi[$n])) {$im=$bi[$n];}
    $r.='<tr><td>'.$ans.':<input class="btnimg" n="'.$n.'" id="btnimg'.$n.'" name="btnimg'.$n.'" value="'.$im.'" type="hidden"></td>';
    $r.='<td width="50"><img id="bimg'.$n.'" style="width:30px;height:30px;" src="'.$im.'" onclick="AjexFileManager1.open({returnTo: \'$btnimg'.$n.'\'});"></td>';
    $r.='<td><a href="#" onclick="ClearImage ('.$n.');">X</a></td></tr>';
    $n++;
  }
  $r.='</table><input id="btnimages" name="btnsimages" type="hidden" value="'.$btnimages.'">';
  $r.='<script>';
  $r.='
  function imgch(fn,a,b)
  {  	console.log ("imgch:",fn," a:",a," b:",b);
    var n = $("#"+a).attr("n");
    console.log ("Change:",n);
    $("#bimg"+n).attr("src",$("#"+a).val());
    SetColors();
  }';
  $r.='</script>';
  $page['vars']['btnimgsel']=$r;

  return '%%btnimgsel%%';
}
function SkoGenerateDesignWorkA($id_q,$idu)
{
  global $page;
  $page['head'].='<link href="css/evol.colorpicker.css" rel="stylesheet" />';
  $page['head'].='<script src="js/evol.colorpicker.js" type="text/javascript"></script>';
  $page['head'].='<script src="js/qdesign.js" type="text/javascript"></script>';
  $page['head'].='<script src="js/AjexFileManager/ajex1.js" type="text/javascript"></script>';

  $sql='select id_q, tmpl,bgcolor,btncolor,qfont,afont, q_offs, a_offs,bgimage,bgbimage,answers,btnimages,btnpadding from sko_questions where (id='.$id_q.' and id_user='.$idu.')';
  $R = mysql_query_my ($sql) or die ("Error in SkoGenerateDesignWorkA<br>".mysql_error());
  $T=mysql_fetch_array($R);
  $answers=$T['answers'];
  $btnimages=$T['btnimages'];
  $id_quest=$T['id_q'];
  $id_tmpl=$T['tmpl'];
  $qfont=$T['qfont'];
  $afont=$T['afont'];
  $bgcolor=$T['bgcolor'];
  $btncolor=$T['btncolor'];

  $bgimage=$T['bgimage'];
  $bgbimage=$T['bgbimage'];

  $qo=explode ('|',$T['q_offs']);
  $ao=explode ('|',$T['a_offs']);
  $bp=explode ('|',$T['btnpadding']);
  $offsx1=$qo[0];
  $offsy1=$qo[1];
  $offsx2=$ao[0];
  $offsy2=$ao[1];

  $r='<center><button idq="'.$id_quest.'" id="returnBtn" class="mybtn">Вернуться к вопросам</button></center><br>';
  $r.='<form action="" method="POST">';
  $r.='<table class="tbl_wa">';


  $tmpls='<select size="1" name="tmpl" style="width:100%;">';
  $sql='select id, name from sko_tmpls order by id';
  $R = mysql_query_my ($sql) or die ("Error in SkoGenerateDesignWorkA<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
    {
      $s='';
      if ($id_tmpl==$T['id']) {$s=' selected';}
      $tmpls.='<option value="'.$T['id'].'"'.$s.'>'.$T['name'].'</option>';
 	  $T=mysql_fetch_array($R);
    }
  $tmpls.='</select>';

  $bgcolor='<input name="bgcolor" id="cpBoth" value="'.$bgcolor.'" />';
  $btncolor='<input name="btncolor" id="cpBoth1" value="'.$btncolor.'" />';
  $qtfont=SKoGenerateFont($qfont,'q');
  $anfont=SKoGenerateFont($afont,'a');

  $qtoffs=SkoGenerateOffs($offsx1,$offsy1,'q');
  $qtoffs1=SkoGenerateOffs($offsx2,$offsy2,'a');
  $btpadd=SkoGenerateOffs($bp[0],$bp[1],'bp');

  $bgimage=SkoGenerateBGImage($bgimage);
  $bgbimage=SkoGenerateBGImage($bgbimage,true);

  $r.='<tr><td>Шаблон:</td><td>'.$tmpls.'</td></tr>';
  $r.='<tr><td>Цвет фона:</td><td>'.$bgcolor.'</td></tr>';
  $r.='<tr><td>Картинка фона:</td><td>'.$bgimage.'</td></tr>';
  $r.='<tr><td colspan="2"><hr></td></tr>';
  $r.='<tr><td>Шрифт вопроса:</br></br>Тень:</td><td>'.$qtfont.'</td></tr>';
  $r.='<tr><td>Смещение:</td><td>'.$qtoffs.'</td></tr>';
  $r.='<tr><td colspan="2"><hr></td></tr>';
  $r.='<tr><td>Цвет кнопок:</td><td>'.$btncolor.'</td></tr>';
  $r.='<tr><td>Фон кнопок:</td><td>'.$bgbimage.'</td></tr>';
  $r.='<tr><td>Шрифт ответов:</td><td>'.$anfont.'</td></tr>';
  $r.='<tr><td>Смещение:</td><td>'.$qtoffs1.'</td></tr>';
  $r.='<tr><td>Отступы:</td><td>'.$btpadd.'</td></tr>';

  $r.='<tr><td colspan="2"><hr><center><b>Картинки ответов</b></td></tr>';
  $r.='<tr><td colspan="2">'.ButtonsImageSelect($answers,$btnimages).'</td></tr>';
  $r.='</table>';
  $r.='<input name="docomment" type="hidden" value="design-post">';
  $r.='<input class="mybtn" type="submit" value="Сохранить">';
  $r.="</form>";
  $page['vars']['InitFM']='<script type="text/javascript">InitBGSelect();</script>';
  $r.="%%InitFM%%";
  return $r;
}
function SkoDesignPost($id_q,$idu)
{  $r='';
  //$r.='<pre>'.print_r($_POST,true).'</pre>';
  $tmpl=GetPostGetParamINT('tmpl');
  $bgcolor=GetPostGetParamSTR('bgcolor');
  $btncolor=GetPostGetParamSTR('btncolor');
  $q_font=GetPostGetParamSTR('q_font');
  $q_size=GetPostGetParamINT('q_size');
  $q_color=GetPostGetParamSTR('q_color');
  $q_shcolor=GetPostGetParamSTR('q_shcolor');
  $q_shsize=GetPostGetParamINT('q_shsize');
  $a_font=GetPostGetParamSTR('a_font');
  $a_size=GetPostGetParamINT('a_size');
  $a_color=GetPostGetParamSTR('a_color');
  $a_shcolor=GetPostGetParamSTR('a_shcolor');
  $a_shsize=GetPostGetParamINT('a_shsize');
  $q_offsx=GetPostGetParamINT('q_offsx');
  $q_offsy=GetPostGetParamINT('q_offsy');
  $a_offsx=GetPostGetParamINT('a_offsx');
  $a_offsy=GetPostGetParamINT('a_offsy');
  $bgimage=GetPostGetParamSTR('srcFile_function');
  $bgbimage=GetPostGetParamSTR('srcFile_functionb');
  $btnsimages=GetPostGetParamSTR('btnsimages');

  $p_offsx=GetPostGetParamINT('bp_offsx');
  $p_offsy=GetPostGetParamINT('bp_offsy');


  $qf=$q_font.'|'.$q_size.'|'.$q_color.'|'.$q_shcolor.'|'.$q_shsize;
  $af=$a_font.'|'.$a_size.'|'.$a_color.'|'.$a_shcolor.'|'.$a_shsize;
  $q_offs=$q_offsx.'|'.$q_offsy;
  $a_offs=$a_offsx.'|'.$a_offsy;
  $p_offss=$p_offsx.'|'.$p_offsy;
  $sql='update sko_questions set tmpl='.$tmpl.',bgcolor="'.$bgcolor.'",btncolor="'.$btncolor.'",qfont="'.$qf.'",afont="'.$af.'",q_offs="'.$q_offs.'",a_offs="'.$a_offs.'",bgimage="'.$bgimage.'", bgbimage="'.$bgbimage.'", btnimages="'.$btnsimages.'",btnpadding="'.$p_offss.'" where (id='.$id_q.' and id_user='.$idu.')';
  $R = mysql_query_my ($sql) or die ("Error in SkoDesignPost<br>".mysql_error());

  return $r;
}
function SKOGenerateDev()
{ $r='Размеры экрана:';
 $r.='<select id="dev" size="1" name="dev">';
 if (isset($_COOKIE["dev"])) {$id_dev=htmlspecialchars($_COOKIE["dev"]);} else {$id_dev=1;}
 $sql='select id, name, size from sko_devices order by id';
 $R = mysql_query_my ($sql) or die ("Error in SKOGenerateDev<br>".mysql_error());
 $T=mysql_fetch_array($R);
  while (is_array($T))
    {
      $s='';
      if ($id_dev==$T['id']) {$s=' selected';}
      $sz=explode (' ',$T['size']);
      $sz=explode ('x',$sz[0]);
      $w=$sz[0];
      $h=$sz[1];
      $r.='<option w="'.$w.'" h="'.$h.'" value="'.$T['id'].'"'.$s.'>'.$T['name'].' '.$T['size'].'</option>';
 	  $T=mysql_fetch_array($R);
    }
  $s='';
  if (isset($_COOKIE["rotate"])&&$_COOKIE["rotate"]=="true") {$s=' checked';}
  $r.='</select> Повернуть:<input id="rotate" name="rotate" type="checkbox"'.$s.'>';


 return $r;
}
function SkoDesign($par)
{  global $page;
  $id_q=$page['vi1'];
  if ($id_q=="") {$id_q="-1";}
  $idu=$page['user']['id'];
  $do=GetPostGetParamSTR('docomment');
  $r='';
  if ($do=="design-post") {$r.=SkoDesignPost($id_q,$idu);}

  $dev=SKOGenerateDev();

  $wa=SkoGenerateDesignWorkA($id_q,$idu);
  $r.='<table><tr><td style="width:230px;vertical-align:top;">'.$wa.'</td>';
  $r.='<td style="vertical-align:top;">';
  $r.=$dev.'<div id="canvas" style="width:500px;height:250px;display:block;border: 14px #000000 solid;border-radius: 20px 20px 20px 20px;-webkit-border-radius: 20px 20px 20px 20px;-moz-border-radius: 20px 20px 20px 20px;">';
  $r.=GenereateDesign($id_q,$idu);
  $r.='</div></td></tr></table>';


  return $r;
}

?>