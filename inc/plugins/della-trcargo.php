<?php
function GetCountryAndRegionIDs ($id_t,&$id_c,&$id_r)
{
 $R = mysql_query_my ('select country_id,region_id from t_city where (city_id='.$id_t.')') or die ("Error in GetCountryAndRegionIDs<br>".mysql_error().'<br>');
 $T=mysql_fetch_array($R);
 if (is_array($T))
   {
     $id_c=$T['country_id'];     $id_r=$T['region_id'];
   }
}
function SearchCitysHtml($par)
{
 $r='<table width="100%"><tr>';
 $t=array();
 $t=explode (' ',$par);
 $out='';
 $b='t_'.$t[0].'_out';
 $sql='select '.$b.'.id_city, t_city.name as g
from '.$b.'
INNER JOIN t_city on t_city.city_id='.$b.'.id_city
where '.$b.'.id_cargo='.$t[1];
 $R = mysql_query_my ($sql) or die ("Error in CalculateDistance<br>".mysql_error().'<br>'.$sql);
 $T=mysql_fetch_array($R);
 while (is_array($T))
    {
     $out.=$T['g'].'<br>';
 	 $T=mysql_fetch_array($R);
    }
 $r.='<td valign="top">'.$out.'</td>';
 $r.='<td> - </td>';
 $out='';
 $b='t_'.$t[0].'_in';
 $sql='select '.$b.'.id_city, t_city.name as g
from '.$b.'
INNER JOIN t_city on t_city.city_id='.$b.'.id_city
where '.$b.'.id_cargo='.$t[1];
 $R = mysql_query_my ($sql) or die ("Error in CalculateDistance<br>".mysql_error().'<br>'.$sql);
 $T=mysql_fetch_array($R);
 while (is_array($T))
    {
     $out.=$T['g'].'<br>';
 	 $T=mysql_fetch_array($R);
    }
 $r.='<td valign="top">'.$out.'</td>';
 $r.='</tr></table>';
 return $r;
}
function CalculateDistance ($table,$id)
{ $dis=0;
 $d=array();
 $sql='select '.$table.'_out.id_city, t_city.name as g, t_country.name as s
from '.$table.'_out
INNER JOIN t_city on t_city.city_id='.$table.'_out.id_city
INNER JOIN t_country on t_city.country_id=t_country.country_id
where '.$table.'_out.id_cargo='.$id;
 $R = mysql_query_my ($sql) or die ("Error in CalculateDistance<br>".mysql_error().'<br>'.$sql);
 $T=mysql_fetch_array($R);
 while (is_array($T))
    {
     $d[]=$T['g'].', '.$T['s'];
 	 $T=mysql_fetch_array($R);
    }
 $sql='select '.$table.'_in.id_city, t_city.name as g, t_country.name as s
from '.$table.'_in
INNER JOIN t_city on t_city.city_id='.$table.'_in.id_city
INNER JOIN t_country on t_city.country_id=t_country.country_id
where '.$table.'_in.id_cargo='.$id;
 $R = mysql_query_my ($sql) or die ("Error in CalculateDistance<br>".mysql_error().'<br>'.$sql);
 $T=mysql_fetch_array($R);
 while (is_array($T))
    {
     $d[]=$T['g'].', '.$T['s'];
 	 $T=mysql_fetch_array($R);
    }
  $from='';$dest='';$way='';
  foreach($d as $key=>$x)
  {    if ($from=='') {$from=$x;}
      else
        {
          $way=$way.'|'.$dest;
          $dest=$x;        }
  }
  $way=substr($way,1);  $url='origin='.urlencode($from).'&destination='.urlencode($dest).'&sensor=false';
  if ($way!='') {$url.='&waypoints='.urlencode($way);}
  $url='http://maps.googleapis.com/maps/api/directions/json?'.$url.'&language=ru';
  $js=file_get_contents($url);
  $js=json_decode($js);
  foreach($js->{'routes'} as $key1=>$x1)
  {
  foreach($x1->{'legs'} as $key=>$x)
  {
    $dis=$dis+$x->{'distance'}->{'value'};  }
  }
  //echo "$dis";
  return $dis;
}

function GenerateTownsList ($class)
{
  global $page;
  //echo "($class)";
  $r='<script type="text/javascript">
    function towncheck'.$class.'(ele) %[%

    var el = document.getElementById("'.$class.'"+ele);
    el.style.display = (el.style.display == "none") ? "block" : "none";

    var e=ele+1;
    var el = document.getElementById("tr'.$class.'"+e);
    el.style.display = (el.style.display == "none") ? "table-row" : "none";
  %]% </script>';

  $r.='<script type="text/javascript">';
  for ($i=1;$i<10;$i++)
  {
  $r.='
  $(function() %[%
    $( "#'.$class.$i.'" ).autocomplete(%[%
      source: "gc.php",
      select: function( event, ui ) %[%
        $( "#h'.$class.$i.'" ).val( ui.item.id );
        $(this).val( ui.item.value );
        return false;
        %]%,
         minLength: 2,
    %]%);
  %]%);
  ';
  }
  $r.='</script>';
  GetCityDataEx ($class,1,$id_city, $city);
  $r.='<table width="99%" border=0 style="border-bottom:0;">';
  $r.='<tr><td style="text-align:right;width:150px;">Нас. пункт погрузки:</td><td><input class="cargofields" style="width:100%;" id="'.$class.'1" name="'.$class.'1" type="text" value="'.$city.'"><input id="h'.$class.'1" name="id'.$class.'1" type="hidden" value="'.$id_city.'"></td></tr>';
  $i=1;
  $p=true;
  for ($i=2;$i<10;$i++)
  {
     GetCityDataEx ($class,$i,$id_city, $city);
     if ($id_city==-1) {
     	  $d='style="display: none;"';
     	  $ch='';$h=' display:none;';
     	  if ($p) {$d='';}
     	  $p=false;
     }  else {$d='';$ch=" checked";$h='';$p=true;}
     $r.='<tr id="tr'.$class.$i.'" '.$d.'><td style="text-align:right;width:150px;"><input id="towncheck" name="ch" type="checkbox" value="OFF" onclick="towncheck'.$class.'('.$i.');"'.$ch.'> '.$i.'-й пункт погрузки:</td><td id="td'.$class.$i.'" ddisplay: none;"><input class="cargofields" style="width:100%;'.$h.'" id="'.$class.$i.'" name="'.$class.$i.'" type="text" value="'.$city.'"><input id="h'.$class.$i.'" name="id'.$class.$i.'" type="hidden" value="'.$id_city.'"></td></tr>
     ';
  }

  $r.='</table>
  ';
  return $r;
}
function GenerateDatePicHTML ()
{
 global $page;
  $page['head'].='<script src="js/dt-ru.js"></script><script>
  $(function() {
    $( "#from" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#to" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#to" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        $( "#from" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });
  </script>';
 return '<label for="from">С:</label>
<input class="cargofields" style="width:90px;" type="text" id="from" name="from" value="'.$page['cargo']['dt1'].'">
<label for="to">по:</label>
<input class="cargofields" style="width:90px;" type="text" id="to" name="to" value="'.$page['cargo']['dt2'].'">';
}
function GenerateCargoTransHTML($transp=false)
{
  global $page;
  //$page['cargo']['type'];
 $tr='<table border=0>';
 $tr.='<tr><td style="width:130px;text-align:right;"><b>Тип транспорта:</b></td><td><select class="cargofields" style="width:99%;" size="1" name="transp">';
 $R = mysql_query_my ('SELECT id,transport,st from t_transptype order by st,transport') or die ("Error in GenerateCargoTransHTML<br>".mysql_error());
 $T=mysql_fetch_array($R);
 $raz=true;
 while (is_array($T))
    {
     $sel='';
     if ($T['id']==$page['cargo']['type']) {$sel=' selected';}
     $tr.='<option value="'.$T['id'].'"'.$sel.'>'.$T['transport'].'</option>';
     if ($T['st']==1&&$raz) {$tr.='<option value="0" disabled="disabled">-------------------------</option>';$raz=false;}
 	 $T=mysql_fetch_array($R);
    }

 // '<option value=""></option>';
 $tr.='</select></td></tr>';
 if ($transp)
 {
   $ta=array('грузовик','полуприцеп','сцепка');
   $tta='';
   foreach($ta as $key=>$x)
   {
      if ($page['cargo']['type_a']==$key) {$sl=' checked';} else {$sl='';}   	  $tta.='<input name="type_a" type="radio" value="'.$key.'"'.$sl.'> '.$x.'&nbsp;';
   }
   $tr.='<tr><td></td><td>'.$tta.'</td></tr>';
 }

 $tr.='<tr><td style="width:130px;text-align:right;"><b>Кол-во машин:</b></td><td><input class="cargofields" name="trcnt" type="text" value="'.$page['cargo']['cnt'].'"></td></tr>';
 $raz='<table style="width:99%;">';

 if ($transp) {$gr='габариты транспортного средства, в метрах';} else {$gr='размеры груза, в метрах';}
 $raz.='<tr><td style="font-size:1em;" colspan=3><b>'.$gr.'</b></td></tr>';
 $raz.='<tr><td style="font-size:1em;width:33%;">длина:<input class="cargofields" style="font-size:1em;width:75px;" name="c-len" type="text" value="'.$page['cargo']['l'].'">м.</td><td style="font-size:1em;width:33%;">ширина:<input class="cargofields" style="font-size:1em;width:75px;" name="c-width" type="text" value="'.$page['cargo']['w'].'">м.</td><td style="font-size:1em;width:33%;">высота:<input class="cargofields" style="font-size:1em;width:75px;" name="c-height" type="text" value="'.$page['cargo']['h'].'">м.</td></tr>';
 $raz.='</table>';
 $tr.='<tr><td colspan=2>'.$raz.'</td></tr>';
 $tr.='</table>';
 $har='<table border=0>';
 if (!$transp) {
   $har.='<tr><td style="width:130px;text-align:right;"><b>Характер груза:</b></td><td><input class="cargofields" style="width:99%;" name="cargo" type="text" value="'.$page['cargo']['cargo'].'"></td></tr>';
   $s_m='вес груза (т)';
   $s_v='объем груза (м³)';
 }
   else
   {     $s_m='Грузоподъемность (т)';
     $s_v='Полезный объем (м³)';
   }
 $har.='<tr><td style="width:170px;text-align:right;"><b>'.$s_m.':</b></td><td><input class="cargofields" name="weight" type="text" value="'.$page['cargo']['weight'].'"></td></tr>';
 $har.='<tr><td style="width:170px;text-align:right;"><b>'.$s_v.':</b></td><td><input class="cargofields" name="value" type="text" value="'.$page['cargo']['vol'].'"></td></tr>';
 $har.='<tr><td colspan=2><label id="pricelabel">&nbsp;</label> <a href="#" onClick="$(\'#pricedialog\').dialog(\'open\');return true;">Указать цену</a></td></tr>';
 $har.='<tr><td colspan=2><label id="addlabel">&nbsp;</label> <a href="#" onClick="$(\'#dialog\').dialog(\'open\');return true;">Дополнительные параметры</a></td></tr>';
 $har.='</table>';

 $r='<tr><td width="450" valign="top">'.$har.'</td><td width="20"></td><td width="450" valign="top"><b>Тип транспорта:</b>'.$tr.'</td></tr>';

 return $r;
}
function GenerateCargoDiagFields($transp=false)
{
  $r='<table width="750">';
  $n=0;
  if ($transp) {$t='t_trnsportaddf';} else {$t='t_cargoaddf';}

  $R = mysql_query_my ('select id,field,tp from '.$t.' order by pos') or die ("Error in GenerateCargoDiagFields<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
    {
     if ($n==0) {$r.='<tr>';}
     $n++;
     $r.='<td class="addfield">';
     $op=GetAddPriceParam ('a','addf'.$T['id'],$v,'');
     if ($op=="Y") {$ch=" checked";} else {$ch='';}
     if ($T['tp']=="CH") {$r.='<input id="addf'.$T['id'].'" name="addf'.$T['id'].'" type="checkbox"'.$ch.'> <label id="addf'.$T['id'].'l">'.$T['field'].'</label>';}
     if ($T['tp']=="TX") {$r.='<table border=0 width="100%"><tr><td style="width:100px;" class="addfield"><input id="addf'.$T['id'].'" name="addf'.$T['id'].'" type="checkbox"'.$ch.'> <label id="addf'.$T['id'].'l">'.$T['field'].'</label></td><td><input style="width:100px;align:right;" id="addf'.$T['id'].'v" class="cargofields" name="addf'.$T['id'].'" type="text" value="'.$v.'"></td></tr></table>';}
     if ($T['tp']=="PL") {$r.='<table width="100%"><tr><td class="addfield"><input id="addf'.$T['id'].'" name="addf'.$T['id'].'" type="checkbox"'.$ch.'> <label id="addf'.$T['id'].'l">'.$T['field'].'</label></td><td><select id="addf'.$T['id'].'v" size="1" class="cargofields" name="addf'.$T['id'].'">
              <option value="">не указан</option>
              <option value="EUR 1,2 x 0,8 м">EUR 1,2 x 0,8 м</option>
              <option value="EUR2 1,2 x 1,0 м">EUR2 1,2 x 1,0 м</option>
              <option value="ISO 1,1 x 1,1 м">ISO 1,1 x 1,1 м</option>
              <option value="ISO 1,0 x 1,0 м">ISO 1,0 x 1,0 м</option></td></tr></table>';}
     $r.='</td>';
     if ($n==3) {$r.='</tr>';$n=0;}
 	 $T=mysql_fetch_array($R);
    }
  if ($n!=3) {$r.='</tr>';$n=0;}

  $r.='</table>';
  return  $r;
}
function GenerateModalCargoDialog($transp=false)
{
  global $page;

  $page['head'].='<script>
$(function() {
$("#dialog").dialog({
	autoOpen: false,
	title: " Дополнительная информация",  //тайтл, заголовок окна
	width:750,
	modal: true           //булева переменная если она равно true -  то окно модальное, false -  то нет
});  });
$(document).ready(function(){
    $("#saveadd").click(function(){
        var s,idf,h;
        s="";
        h="addf0|";
    	$("input:checkbox:checked").each(function (i) {
    	   hp=this.id;
    	   if (hp!="addprf"&&hp!="towncheck")
    	   {
    	   idf="#"+this.id;
    	   var str = $(idf+"l").text();
    	   var theClass = $(idf+"v").attr("class");
    	   var v=$(idf+"v");
    	   if (theClass =="cargofields") {str=str+"="+v.val();hp=hp+"="+v.val();}
    	   h=h+hp+"|";
    	   //str=str+"("+theClass+")";
    	   s=s+str+"; ";
    	   }
    	});
    	var note=$("#addnote").val();
    	$("#note").val(note);

    	$("#addlabel").text (s);
    	$("#addhiden").val(h);
 	    $("input[name=addftxt]").val (s);

        $("#dialog").dialog("close");
    })
    $("#saveadd").click();
});

  </script>';

  $r='<div id="dialog" title="Дополнительная информация" style="width:610px;">'.GenerateCargoDiagFields($transp).'Примечание:<input id="addnote" name="addnote" class="cargofields" type="text" value="'.$page['cargo']['note'].'"><button id="saveadd">Сохранить</button></div>';

  return $r;
}

function GenerateEdizmHTML($sid)
{
  global $page;
  //$page['cargo'][''];

  $r='';
  $R = mysql_query_my ('select id,txt from t_priceed') or die ("Error in GenerateEdizmHTML<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
    {
      $s='';
      if ($sid==$T['id']) {$s=" selected";}
      $r.='<option value="'.$T['id'].'"'.$s.'>'.$T['txt'].'</option>';
  	 $T=mysql_fetch_array($R);
    }


  return  $r;
}
function GenerateAddPriceFields()
{
  $r='';
  $R = mysql_query_my ('select id,txt,type from t_addpricef') or die ("Error in GenerateAddPriceFields<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
    {
      $s='';
   	  $op=GetAddPriceParam('p','addpricef'.$T['id'],$v,'');
   	  if ($op=="Y") {$ch=" checked";} else {$ch="";}
      if ($T['type']=="CH") {$r.='<input id="addprf" name="addpricef'.$T['id'].'" type="checkbox"'.$ch.'><label id="addpricef'.$T['id'].'l"> '.$T['txt'].'</label></br>';}
      if ($T['type']=="TX") {$r.='<input id="addprf" name="addpricef'.$T['id'].'" type="checkbox"'.$ch.'><label id="addpricef'.$T['id'].'l"> '.$T['txt'].'</label> <input id="addpricef'.$T['id'].'v" name="addpricef'.$T['id'].'v" type="text" value="'.$v.'" class="cargofields" style="width:60px;">%</br>';}
  	 $T=mysql_fetch_array($R);
    }


  return  $r;


}
function GetAddPriceParam ($w,$p,&$v,$d)
{
  global $page;
  $r='';
  $v=$d;
  foreach($page['cargo'][$w] as $k=>$x)
  {
    $e=explode('=',$x);
    if ($e[0]==$p)
    {
      $r="Y";
      if (isset($e[1])) {$v=$e[1];}
      break;
    }

  }
 return $r;
}

function GeneratePriceDiagFields()
{
  global $page;
  $r='<table width="100%" border=0>';
  $lch=array('','');
  $op=GetAddPriceParam('p','priceradio',$v,0);
  $lch[$v]=' checked';

  $r.='<tr><td colspan=2 width="66%"><input name="priceradio" type="radio" value="0"'.$lch[0].'>Указать стоимость перевозки и форму оплаты</td>';
  $r.='<td width="33%"><input name="priceradio" type="radio" value="1"'.$lch[1].'>Не указывать стоимость перевозки(цена договорная)</td></tr>';
  $op=GetAddPriceParam('p','price',$v,'');
  $op1=GetAddPriceParam('p','ed',$v1,'-1');

  $r.='<tr><td width="33%" valign="top"><input name="price" class="cargofields" type="text" value="'.$v.'" style="width:100px;"> - <select id="seled" class="cargofields" style="width:100px;" size="1" name="ediz">'.GenerateEdizmHTML($v1).'</select></br>';
  $list=array('б/н','нал.','комбинир.','софт','удобная','на карту');
  $op=GetAddPriceParam('p','ptype',$v,$list[0]);
  foreach($list as $p =>$x)
  {
     $ch='';
     if ($x==$v) {$ch=' checked';}
  	 $r.='<input name="pricetype" type="radio" value="'.$x.'"'.$ch.'> '.$x.' ';
  }

  $r.='<td width="33%" valign="top">'.GenerateAddPriceFields().'</td>';

  $op=GetAddPriceParam('p','pricequery',$v,'0');
  if ($v=="1")  {$ch=" checked";} else {$ch='';}
  $r.='<td width="33%" valign="top"><input name="pricequery" type="checkbox" value="pq"'.$ch.'> Запрос цены</td>';


  $r.='</tr>';
  $r.='</table>';
  return $r;
}
function GenerateModalPriceDialog()
{
  global $page;

  $page['head'].='<script>
$(function() {
$("#pricedialog").dialog({
	autoOpen: false,
	title: "   Указать цену",  //тайтл, заголовок окна
	width:750,
	modal: true           //булева переменная если она равно true -  то окно модальное, false -  то нет
});  });

$(document).ready(function(){
    $("#saveprice").click(function(){
        var s,idf,h;
        var r=$("input[name=priceradio]:checked").val();
        s="";
        h="";
        if (r==0) {
        	 s="";
        	 h="priceradio=0|";
        	 s=s+"Цена:"+$("input[name=price]").val()+" "+$("#seled option:selected").text()+"; ";
        	 h=h+"price="+$("input[name=price]").val()+"|";
        	 h=h+"ed="+$("#seled option:selected").val()+"|";
        	 var r=$("input[name=pricetype]:checked").val();
        	 s=s+r+"; ";
        	 h=h+"ptype="+$("input[name=pricetype]:checked").val()+"|";
        	 //addprf

         	$("input:checkbox:checked").each(function (i) {
    	     hp=this.id;
    	     if (hp=="addprf")
         	   {
        	   idf="#"+this.name;
        	   hp=this.name;
        	   var str = $(idf+"l").text();
    	       var theClass = $(idf+"v").attr("class");
    	       var v=$(idf+"v");
    	       if (theClass =="cargofields") {str=str+"="+v.val();hp=hp+"="+v.val();}
       	       h=h+hp+"|";
    	       s=s+str+"; ";
    	     }
    	   });


        	}
          else {
          	 s="Цена не указана; ";
          	 h="priceradio=1|";
          	 if ($("input[name=pricequery]").is(":checked")) {s=s+"Указать цену";h=h+"pricequery=1|";}
          	 }

    	$("#pricelabel").text (s);
    	$("input[name=pricehiden]").val (h);
    	$("input[name=pricetxt]").val (s);

        $("#pricedialog").dialog("close");
    })
    $("#saveprice").click();
});

  </script>';

  $r='<div id="pricedialog" title="Указать цену" style="width:610px;">'.GeneratePriceDiagFields().'<button id="saveprice">Сохранить</button></div>';

  return $r;
}

function DatePickTDateStr ($d)
{
  $r='';
  $a=explode ('/',$d);
  if (count($a)==3)
  {
  for ($i=0;$i<3;$i++) {$a[$i]=$a[$i]+0;}
  $r=$a[2].'-'.$a[1].'-'.$a[0];
  }
  return $r;
}
function DatePickTDateStrUndo ($d)
{
  $a=explode ('-',$d);
  for ($i=0;$i<3;$i++) {$a[$i]=$a[$i];}
  return $a[2].'/'.$a[1].'/'.$a[0];
}
function CargoPost()
{
  global $page;
  $page['error']='';
  $id=GetPostGetParam('id_cargo')+0;
  $id_company=$page['vars']['id_company'];
  if ($id==-1)
     {
        $R = mysql_query_my ('insert into t_cargos (id_company) values (-500)') or die ("CargoPost step 1.0<br>".mysql_error());
        $R = mysql_query_my ('select LAST_INSERT_ID() as id from t_cargos') or die ("CargoPost step 1.1<br>".mysql_error());
        $T=mysql_fetch_array($R);
        if (!is_array($T))
          {
             $page['error']='Ошибка сервера базы данных';
             return -1;
          }
       $id=$T['id'];

     }
  $db='ID:'.$id.' id_company:'.$id_company;
  $dt1=DatePickTDateStr (GetPostGetParam('from'));
  $dt2=DatePickTDateStr (GetPostGetParam('to'));

  $cargo=mysql_real_escape_string(GetPostGetParam('cargo'));
  $weight=mysql_real_escape_string(GetPostGetParam('weight'));
  $value=mysql_real_escape_string(GetPostGetParam('value'));
  $transp=GetPostGetParam('transp')+0;
  $trcnt=GetPostGetParam('trcnt')+0;
  $c_l=mysql_real_escape_string(GetPostGetParam('c-len'));
  $c_w=mysql_real_escape_string(GetPostGetParam('c-width'));
  $c_h=mysql_real_escape_string(GetPostGetParam('c-height'));
  $note=mysql_real_escape_string(GetPostGetParam('note'));
  $pricehiden=mysql_real_escape_string(GetPostGetParam('pricehiden'));
  $addhiden=mysql_real_escape_string(GetPostGetParam('addhiden'));

  $addtxt=mysql_real_escape_string(GetPostGetParam('addftxt'));
  $pricetxt=mysql_real_escape_string(GetPostGetParam('pricetxt'));

  //$distance=CalculateDistance ('t_cargos',$id);
  $distance=0;

  $sql='update t_cargos set id_company='.$id_company.', dt1="'.$dt1.'",dt2="'.$dt2.'",cargo="'.$cargo.'",weight="'.$weight.'",vol="'.$value.'",type='.$transp.',cnt='.$trcnt.',l="'.$c_l.'",w="'.$c_w.'",h="'.$c_h.'",note="'.$note.'",addf="'.$addhiden.'",price="'.$pricehiden.'", addftxt="'.$addtxt.'", pricetxt="'.$pricetxt.'", distance="'.$distance.'" where (id="'.$id.'")';
  $R = mysql_query_my ($sql) or die ("CargoPost step 2.0<br>".mysql_error());

  mysql_query_my ('delete from t_cargos_out where (id_cargo='.$id.')') or die ("CargoPost step 3.0<br>".mysql_error());
  for ($i=1;$i<10;$i++)
  {
    $idt=GetPostGetParam('idfromtowns'.$i)+0;
    if ($idt>0)
      {
        $id_c=-1;$id_r=-1;
        GetCountryAndRegionIDs ($idt,$id_c,$id_r);
    	mysql_query_my ('insert into t_cargos_out (id_cargo,id_city,id_country,id_region) values ('.$id.','.$idt.','.$id_c.','.$id_r.')') or die ("CargoPost step 3.1<br>".mysql_error());
       }
  }

  mysql_query_my ('delete from t_cargos_in where (id_cargo='.$id.')') or die ("CargoPost step 4.0<br>".mysql_error());
  for ($i=1;$i<10;$i++)
  {
    $idt=GetPostGetParam('idtotowns'.$i)+0;
    if ($idt>0)
      {
        $id_c=-1;$id_r=-1;
        GetCountryAndRegionIDs ($idt,$id_c,$id_r);
    	mysql_query_my ('insert into t_cargos_in (id_cargo,id_city,id_country,id_region) values ('.$id.','.$idt.','.$id_c.','.$id_r.')') or die ("CargoPost step 4.1<br>".mysql_error());
       }
  }
  $distance=CalculateDistance ('t_cargos',$id);
  $sql='update t_cargos set distance="'.$distance.'" where (id="'.$id.'")';
  $R = mysql_query_my ($sql) or die ("CargoPost step 3.0<br>".mysql_error());


  $db.='<br>dt1:'.$dt1.' dt2:'.$dt2;


  $_POST['res']=$db;
  return $id;
}
function LoadCargoData ($id_cargo)
{
  $c=array();
  $c['allow']=true;
  $R = mysql_query_my ('select * from t_cargos where (id='.$id_cargo.')') or die ("Error in LoadCargoData<br>".mysql_error().'<br>');
  $i = 0;
  while ($i < mysql_num_fields($R)) {
    $meta = mysql_fetch_field($R, $i);
    $c[$meta->name]='';
    $i++;
  }
  $c['cnt']=1;
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
     $i = 0;
     while ($i < mysql_num_fields($R)) {
       $meta = mysql_fetch_field($R, $i);
       $c[$meta->name]=$T[$meta->name];
       $i++;
     }
    $T=mysql_fetch_array($R);
  }
  if ($c['dt1']!="") {$c['dt1']=DatePickTDateStrUndo ($c['dt1']); }
  if ($c['dt2']!="") {$c['dt2']=DatePickTDateStrUndo ($c['dt2']); }
  // Load city`s
  $c['from']=array();
  $i=0;
  $R = mysql_query_my ('select id_city from t_cargos_out where (id_cargo='.$id_cargo.')') or die ("Error in LoadCargoData<br>".mysql_error().'<br>'.$sql);
  $i = 0;
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
    $i++;
    $c['from'][$i]=$T['id_city'];
    $T=mysql_fetch_array($R);
  }
  $i=0;
  $R = mysql_query_my ('select id_city from t_cargos_in where (id_cargo='.$id_cargo.')') or die ("Error in LoadCargoData<br>".mysql_error().'<br>'.$sql);
  $i = 0;
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
    $i++;
    $c['to'][$i]=$T['id_city'];
    $T=mysql_fetch_array($R);
  }
  //Init Prices array
  $c['p']=explode ('|',$c['price']);
  //Init add fields
  $c['a']=explode ('|',$c['addf']);

  //var_dump ($c);
  return $c;
}
function AddEditCargo ($par)
{
  global $page;
  $id_cargo=$page['vi1']+0;
  if ($id_cargo==0) {$id_cargo=-1;}
  //$id_cargo=-1;
  $r='';
  if (GetPostGetParam('docomment')=='CargoEdit')
    {
  	  $id_cargo=CargoPost();
  	  if ($id_cargo==-1) {$r.='<div class="notice error"><i class="icon-remove-sign icon-large"></i> Ошибка сохранения <a href="#close" class="icon-remove"></a></div>';}
  	    else {$r.='<div class="notice success"><i class="icon-ok icon-large"></i> Сохранено<a href="#close" class="icon-remove"></a></div>';}

  	}
  $page['head'].='<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">';
  $page['head'].='<script src="js/jquery-ui-min.js"></script>';
  $page['head'].='<script src="js/jquery.mask.min.js"></script>';

 $c=LoadCargoData ($id_cargo);
 $page['cargo']=$c;
 $totowns=GenerateTownsList ('totowns');
 $fromtowns=GenerateTownsList ('fromtowns');
 $r.='<form method="POST" action="AddEditCargo">'.GenerateModalCargoDialog().GenerateModalPriceDialog();
 $r.='<input name="docomment" type="hidden" value="CargoEdit">';
 $r.='<input name="pricehiden" type="hidden" value="'.$c['price'].'">';
 $r.='<input name="pricetxt" type="hidden" value="">';
 $r.='<input name="addftxt" type="hidden" value="">';
 $r.='<input id="addhiden" name="addhiden" type="hidden" value="'.$c['addf'].'">';
 $r.='<input id="note" name="note" type="hidden" value="'.$c['note'].'">';
 $r.='<input id="id_cargo" name="id_cargo" type="hidden" value="'.$id_cargo.'">';

 $r.='<table width="920" border=0 style="background:#A9E2F3;border-bottom:0;">';
 $r.='<tr><td colspan="3">Укажите пожалуйста населенные пункты погрузки и выгрузки, параметры груза и контактную информацию</td></tr>';
 $r.='<tr><td width="450"><b>Погрузка: '.GenerateDatePicHTML().'</b></td><td width="20"></td><td width="450"><b>Выгрузка:</b></td></tr>';
 $r.='<tr><td width="450" valign="top">'.$fromtowns.'</td><td width="20"></td><td width="450" valign="top">'.$totowns.'</td></tr>';
 $r.=GenerateCargoTransHTML();
 $r.='<tr><td colspan=3><input type="submit" value="Сохранить"></td></tr>';
 $r.='</table></form>';
 //$r.='<pre>'.print_r($_POST,true).'</pre>id_cargo='.$id_cargo;
 return $r;
}
function CargoMap($par, $transp=false)
{
 global $page;
 $r='';
 $sc=GetTagbyNAME ('map-cargo-script');
 $id_c=$page['vi1'];
 if (!$transp) {$d=LoadCargoData ($id_c);} else {$d=LoadTransportData ($id_c);}
 $f=DellaGetCity($d['from'][1],true);
 $w='';
 for ($i=2;$i<10;$i++)
 {
   if (isset($d['from'][$i]))
    {
      $w.="   waypts.push({location:'".DellaGetCity($d['from'][$i],true)."',stopover:true});
      ";
    }
 }
 $t=DellaGetCity($d['to'][1],true);
 for ($i=1;$i<10;$i++)
 {
   if (isset($d['to'][$i]))
    {
      $t1=$t;
      $t=DellaGetCity($d['to'][$i],true);
      if ($t!=$t1)
      $w.="   waypts.push({location:'".$t1."',stopover:true});
      ";
    }
 }
 //if ($w!="") {$sc=str_replace("%%WP%%", 'new Array('.$w.')', $sc);$w="waypoints: w,"; }
 $sc=str_replace("%%START%%", $f, $sc);
 $sc=str_replace("%%END%%", $t, $sc);
 $sc=str_replace("%%WAYP%%", $w, $sc);

 $page['head'].=$sc;
 $page['head'].='<script>
    $(document).ready(function(){
    	initialize();
    	calcRoute();
});

  </script>';
 return $r;
}
// ---------------- TRANSPORT ---------------------------
function TransportMap($par)
{  $r=CargoMap ($par, true);
}
function LoadTransportData ($id_trans)
{  $c=array();
  $c['allow']=true;
  $R = mysql_query_my ('select * from t_transports where (id='.$id_trans.')') or die ("Error in LoadTransportData<br>".mysql_error().'<br>');
  $i = 0;
  while ($i < mysql_num_fields($R)) {
    $meta = mysql_fetch_field($R, $i);
    $c[$meta->name]='';
    $i++;
  }
  $c['cnt']=1;
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
     $i = 0;
     while ($i < mysql_num_fields($R)) {
       $meta = mysql_fetch_field($R, $i);
       $c[$meta->name]=$T[$meta->name];
       $i++;
     }
    $T=mysql_fetch_array($R);
  }
  if ($c['dt1']!="") {$c['dt1']=DatePickTDateStrUndo ($c['dt1']); }
  if ($c['dt2']!="") {$c['dt2']=DatePickTDateStrUndo ($c['dt2']); }
  // Load city`s
  $c['from']=array();
  $i=0;
  $R = mysql_query_my ('select id_city from t_transports_out where (id_cargo='.$id_trans.')') or die ("Error in LoadTransportData<br>".mysql_error());
  $i = 0;
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
    $i++;
    $c['from'][$i]=$T['id_city'];
    $T=mysql_fetch_array($R);
  }
  $i=0;
  $R = mysql_query_my ('select id_city from t_transports_in where (id_cargo='.$id_trans.')') or die ("Error in LoadTransportData<br>".mysql_error());
  $i = 0;
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
    $i++;
    $c['to'][$i]=$T['id_city'];
    $T=mysql_fetch_array($R);
  }
  //Init Prices array
  $c['p']=explode ('|',$c['price']);
  //Init add fields
  $c['a']=explode ('|',$c['addf']);

  //var_dump ($c);
  return $c;
}
function TransportPost()
{  global $page;
  $page['error']='';
  $id=GetPostGetParam('id_transp')+0;
  $id_company=$page['vars']['id_company'];
  if ($id==-1)
     {
        $R = mysql_query_my ('insert into t_transports (id_company) values (-500)') or die ("TransportPost step 1.0<br>".mysql_error());
        $R = mysql_query_my ('select LAST_INSERT_ID() as id from t_transports') or die ("TransportPost step 1.1<br>".mysql_error());
        $T=mysql_fetch_array($R);
        if (!is_array($T))
          {
             $page['error']='Ошибка сервера базы данных';
             return -1;
          }
       $id=$T['id'];

     }
  $db='ID:'.$id.' id_company:'.$id_company;
  $dt1=DatePickTDateStr (GetPostGetParam('from'));
  $dt2=DatePickTDateStr (GetPostGetParam('to'));

  $type_a=mysql_real_escape_string(GetPostGetParam('type_a'))+0;
  $weight=mysql_real_escape_string(GetPostGetParam('weight'));
  $value=mysql_real_escape_string(GetPostGetParam('value'));
  $transp=GetPostGetParam('transp')+0;
  $trcnt=GetPostGetParam('trcnt')+0;
  $c_l=mysql_real_escape_string(GetPostGetParam('c-len'));
  $c_w=mysql_real_escape_string(GetPostGetParam('c-width'));
  $c_h=mysql_real_escape_string(GetPostGetParam('c-height'));
  $note=mysql_real_escape_string(GetPostGetParam('note'));
  $pricehiden=mysql_real_escape_string(GetPostGetParam('pricehiden'));
  $addhiden=mysql_real_escape_string(GetPostGetParam('addhiden'));

  $addtxt=mysql_real_escape_string(GetPostGetParam('addftxt'));
  $pricetxt=mysql_real_escape_string(GetPostGetParam('pricetxt'));

  //$distance=CalculateDistance ('t_transports',$id);
  $distance=0;

  $sql='update t_transports set id_company='.$id_company.', dt1="'.$dt1.'",dt2="'.$dt2.'",type_a="'.$type_a.'",weight="'.$weight.'",vol="'.$value.'",type='.$transp.',cnt='.$trcnt.',l="'.$c_l.'",w="'.$c_w.'",h="'.$c_h.'",note="'.$note.'",addf="'.$addhiden.'",price="'.$pricehiden.'" , addftxt="'.$addtxt.'", pricetxt="'.$pricetxt.'", distance="'.$distance.'" where (id="'.$id.'")';
  $R = mysql_query_my ($sql) or die ("TransportPost step 2.0<br>".mysql_error());

  mysql_query_my ('delete from t_transports_out where (id_cargo='.$id.')') or die ("TransportPost step 3.0<br>".mysql_error());
  for ($i=1;$i<10;$i++)
  {
    $idt=GetPostGetParam('idfromtowns'.$i)+0;
    if ($idt>0)
      {
        $id_c=-1;$id_r=-1;
        GetCountryAndRegionIDs ($idt,$id_c,$id_r);
    	mysql_query_my ('insert into t_transports_out (id_cargo,id_city,id_country,id_region) values ('.$id.','.$idt.','.$id_c.','.$id_r.')') or die ("TransportPost step 3.1<br>".mysql_error());
       }
  }

  mysql_query_my ('delete from t_transports_in where (id_cargo='.$id.')') or die ("TransportPost step 4.0<br>".mysql_error());
  for ($i=1;$i<10;$i++)
  {
    $idt=GetPostGetParam('idtotowns'.$i)+0;
    if ($idt>0)
      {
        $id_c=-1;$id_r=-1;
        GetCountryAndRegionIDs ($idt,$id_c,$id_r);
    	mysql_query_my ('insert into t_transports_in (id_cargo,id_city,id_country,id_region) values ('.$id.','.$idt.','.$id_c.','.$id_r.')') or die ("TransportPost step 4.1<br>".mysql_error());
       }
  }
  $distance=CalculateDistance ('t_transports',$id);
  $sql='update t_transports set distance="'.$distance.'" where (id="'.$id.'")';
  $R = mysql_query_my ($sql) or die ("TransportPost step 3.0<br>".mysql_error());

  $db.='<br>dt1:'.$dt1.' dt2:'.$dt2;


  $_POST['res']=$db;
  return $id;
}
function GenerateModalTransportDialog()
{  $r='';
  return $r;
}
function AddEditTransport($par)
{  global $page;
  $id_transp=$page['vi1']+0;
  if ($id_transp==0) {$id_transp=-1;}
  //$id_cargo=-1;
  $r='';
  if (GetPostGetParam('docomment')=='TransporEdit')
    {
  	  $id_transp=TransportPost();
  	  if ($id_transp==-1) {$r.='<div class="notice error"><i class="icon-remove-sign icon-large"></i> Ошибка сохранения <a href="#close" class="icon-remove"></a></div>';}
  	    else {$r.='<div class="notice success"><i class="icon-ok icon-large"></i> Сохранено<a href="#close" class="icon-remove"></a></div>';}

  	}
  $page['head'].='<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">';
  $page['head'].='<script src="js/jquery-ui-min.js"></script>';
  $page['head'].='<script src="js/jquery.mask.min.js"></script>';

 $c=LoadTransportData ($id_transp);
 $page['cargo']=$c;
 $totowns=GenerateTownsList ('totowns');
 $fromtowns=GenerateTownsList ('fromtowns');
 $r.='<form method="POST" action="AddTransport">'.GenerateModalCargoDialog(true).GenerateModalPriceDialog();
 $r.='<input name="docomment" type="hidden" value="TransporEdit">';
 $r.='<input name="pricehiden" type="hidden" value="'.$c['price'].'">';
 $r.='<input name="pricetxt" type="hidden" value="">';
 $r.='<input name="addftxt" type="hidden" value="">';
 $r.='<input id="addhiden" name="addhiden" type="hidden" value="'.$c['addf'].'">';
 $r.='<input id="note" name="note" type="hidden" value="'.$c['note'].'">';
 $r.='<input id="id_transp" name="id_transp" type="hidden" value="'.$id_transp.'">';

 $r.='<table width="920" border=0 style="background:#A9E2F3;border-bottom:0;">';
 $r.='<tr><td colspan="3">Укажите желаемые пункты погрузки и выгрузки, параметры транспортного стредства и контактную информацию</td></tr>';
 $r.='<tr><td width="450"><b>Погрузка: '.GenerateDatePicHTML().'</b></td><td width="20"></td><td width="450"><b>Выгрузка:</b></td></tr>';
 $r.='<tr><td width="450" valign="top">'.$fromtowns.'</td><td width="20"></td><td width="450" valign="top">'.$totowns.'</td></tr>';
 $r.=GenerateCargoTransHTML(true);
 $r.='<tr><td colspan=3><input type="submit" value="Сохранить"></td></tr>';
 $r.='</table></form>';
 //$r.='<pre>'.print_r($_POST,true).'</pre>id_cargo='.$id_transp.'<pre>'.print_r($page['sqls'],true).'</pre>';
 return $r;
}

?>