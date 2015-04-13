<?php
function MakeCountrySelectHTML($id_contry, $suf)
{
  $p=chr(13).chr(10);  $r=$p.'<select size="1" name="contry'.$suf.'" class="search-combo">'.$p;
  $r.='<option value="-1"'.($id_contry==-1 ? ' selected':'').'>Все страны</option>'.$p;
  $R = mysql_query_my ("select country_id as id,name,pos from t_country order by pos desc,name") or die ("MakeCountrySelectHTML<br>".mysql_error());
  $T=mysql_fetch_array($R);
  $pos=-1;
  while (is_array($T))
    {
     if ($pos!=$T['pos']) {$r.='<option value="-2" disabled="disabled"> - - - - - - - - </option>'.$p;$pos=$T['pos'];}
     $r.='<option value="'.$T['id'].'"'.($T['id']==$id_contry ? ' selected':'').'>'.$T['name'].'</option>'.$p;
 	 $T=mysql_fetch_array($R);
    }
  $r.='</select>'.$p;
  return $r;
}
function MakeAreaSelectHTML($id_contry,$id_area,$suf)
{  $p=chr(13).chr(10);
  $r=$p.'<select size="1" name="area'.$suf.'"'.($id_contry==-1? ' disabled':'').' class="search-combo">'.$p;
  $r.='<option value="-1">Все области</option>'.$p;
  if ($id_contry>0)
  {
  $R = mysql_query_my ("select region_id as id, name from t_region where (country_id=".$id_contry.") order by name") or die ("MakeAreaSelectHTML<br>".mysql_error());
  $T=mysql_fetch_array($R);
  $pos=-1;
  while (is_array($T))
    {
     $r.='<option value="'.$T['id'].'"'.($T['id']==$id_area ? ' selected':'').'>'.$T['name'].'</option>'.$p;
 	 $T=mysql_fetch_array($R);
    }
  }
  $r.='</select>'.$p;
  return $r;
}
function MakeCitySelectHTML($id_contry,$id_area,$id_city,$suf)
{
  $p=chr(13).chr(10);
  $r=$p.'<select size="1" name="city'.$suf.'"'.($id_area==-1? ' disabled':'').' class="search-combo">'.$p;
  $r.='<option value="-1">Все города</option>'.$p;
  if ($id_area>0)
  {
  $R = mysql_query_my ("select city_id as id,name from t_city where (region_id=".$id_area.") order by name") or die ("MakeCitySelectHTML<br>".mysql_error());
  $T=mysql_fetch_array($R);
  $pos=-1;
  while (is_array($T))
    {
     $r.='<option value="'.$T['id'].'"'.($T['id']==$id_city ? ' selected':'').'>'.$T['name'].'</option>'.$p;
 	 $T=mysql_fetch_array($R);
    }
  }
  $r.='</select>'.$p;
  return $r;
}

function GenerateFromToHTML ($id_city, $id_area,$id_contry,$from=true)
{ global $page; $r='<table>';
 $r.='<tr><td style="border-bottom:0px;"><b>'.($from ? 'Откуда':'Куда').':</b></td></tr>';
 $suf=($from ? 'from':'to');
 $r.='<tr><td style="border-bottom:0px;">'.MakeCountrySelectHTML($id_contry,$suf).'</td></tr>';
 $r.='<tr><td style="border-bottom:0px;">'.MakeAreaSelectHTML($id_contry,$id_area,$suf).'</td></tr>';
 $r.='<tr><td style="border-bottom:0px;">'.MakeCitySelectHTML($id_contry,$id_area,$id_city,$suf).'</td></tr>';


 $r.='';
 $r.='';
 $r.='</table>';
 $js=file_get_contents('inc/plugins/inc/dsearch.js');
 $js=str_replace('%countryselect%','contry'.$suf,$js);
 $js=str_replace('%areaselect%','area'.$suf,$js);
 $js=str_replace('%cityselect%','city'.$suf,$js);
 $page['head'].=$js;
 return $r;
}
function GenerateDatasHTML($dateFrom,$dateTo)
{ global $page;
 $page['head'].='<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
 <script src="js/jquery-ui-min.js"></script>
 <script src="js/jquery.mask.min.js"></script>';
 $page['head'].='<script src="js/dt-ru.js"></script>
<script>
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
  }); </script>';
 $r='<table>';
 $r.='<tr><td style="text-align:right;border-bottom:0px;"></td><td style="border-bottom:0px;"><b>Дата:</b></td></tr>';
 $r.='<tr><td style="text-align:right;border-bottom:0px;">с</td><td style="border-bottom:0px;"><input class="cargofields" style="width:90px;" type="text" id="from" name="from" value="'.$dateFrom.'"></td></tr>';
 $r.='<tr><td style="text-align:right;border-bottom:0px;">по</td><td style="border-bottom:0px;"><input class="cargofields" style="width:90px;" type="text" id="to" name="to" value="'.$dateTo.'"></td></tr>';
 $r.='</table>';
 return $r;
}
function GenerateSelectN ($name,$f,$t,$p,$value)
{  $r='<select size="1" name="'.$name.'" class="cargofields">';
  $r.='<option value="-1"'.((-1==$value)?'selected':'').'>.......</option>';
  for ($i=$f;$i<=$t;$i++)
  {
     $r.='<option value="'.$i.'"'.(($i==$value)?'selected':'').'>'.$i.$p.'</option>';
  }
  $r.='</select>';
  return $r;

}
function GenerateMassHTML($mass1,$mass2)
{
 $r='<table>';
 $r.='<tr><td style="text-align:right;border-bottom:0px;"></td><td style="border-bottom:0px;"><b>Масса:</b></td></tr>';
 $r.='<tr><td style="text-align:right;border-bottom:0px;">от</td><td style="border-bottom:0px;">'.GenerateSelectN ('mass1',1,100,' т.',$mass1).'</td></tr>';
 $r.='<tr><td style="text-align:right;border-bottom:0px;">по</td><td style="border-bottom:0px;">'.GenerateSelectN ('mass2',1,100,' т.',$mass2).'</td></tr>';
// $r.='<tr><td style="text-align:right;border-bottom:0px;">от</td><td style="border-bottom:0px;"><input class="cargofields" name="mass1" type="text" value="'.$mass1.'"></td></tr>';
// $r.='<tr><td style="text-align:right;border-bottom:0px;">до</td><td style="border-bottom:0px;"><input class="cargofields" name="mass2" type="text" value="'.$mass2.'"></td></tr>';
 $r.='</table>';
 return $r;
}
function GenerateValueHTML($vol1,$vol2)
{
 $r='<table>';
 $r.='<tr><td style="border-bottom:0px;"></td><td style="border-bottom:0px;"><b>Объем:</b></td></tr>';
 $r.='<tr><td style="text-align:right;border-bottom:0px;">от</td><td style="border-bottom:0px;">'.GenerateSelectN ('vol1',1,100,' м3.',$vol1).'</td></tr>';
 $r.='<tr><td style="text-align:right;border-bottom:0px;">по</td><td style="border-bottom:0px;">'.GenerateSelectN ('vol2',1,100,' м3.',$vol2).'</td></tr>';
 $r.='</table>';
 return $r;
}
function GenerateTransportTypeHTML($ttype)
{
  $r='<select class="cargofields" size="1" name="ttype">';
 $R = mysql_query_my ('SELECT id,transport,st from t_transptype order by st,transport') or die ("Error in GenerateTransportTypeHTML<br>".mysql_error());
 $T=mysql_fetch_array($R);
 $raz=true;
 while (is_array($T))
    {
     $sel='';
     if ($T['id']==$ttype) {$sel=' selected';}
     $r.='<option value="'.$T['id'].'"'.$sel.'>'.$T['transport'].'</option>';
     if ($T['st']==1&&$raz) {$r.='<option value="0" disabled="disabled">-------------------------</option>';$raz=false;}
 	 $T=mysql_fetch_array($R);
    }
  $r.='</select>';
  return $r;
}

function GenereateTypeHTML($type)
{  $r='<center>';
  $ch=' checked';
  $r.='<input name="type" type="radio" value="1"'.(($type==1)?$ch:'').'><b> Грузы </b> &nbsp;&nbsp;&nbsp;&nbsp;';
  $r.='<input name="type" type="radio" value="2"'.(($type==2)?$ch:'').'><b> Транспорт</b>';

  $r.='</center>';
  return $r;
}
function DellaStrToDate ($s)
{
 $d='';    // 05/11/2014
 $a=array();
 $a=explode('/',$s);
 $d=$a[2].'-'.$a[1].'-'.$a[0];
 return $d;
}
function GenerateDateCND ($table, $dateFrom, $dateTo)
{   $r='';
   if (!($dateFrom==""&&$dateTo=""))
     {        if ($dateFrom=="") {$dateFrom="01/01/2000";}
        if ($dateTo=="") {$dateTo="01/01/2050";}
        $d1=DellaStrToDate($dateFrom);
        $d2=DellaStrToDate($dateTo);
        $r='(('.$table.'.dt1 BETWEEN STR_TO_DATE("'.$d1.' 00:00:00", "%Y-%m-%d %H:%i:%s") AND STR_TO_DATE("'.$d2.' 23:59:59", "%Y-%m-%d %H:%i:%s"))';
        $r.=' or ('.$table.'.dt2 BETWEEN STR_TO_DATE("'.$d1.' 00:00:00", "%Y-%m-%d %H:%i:%s") AND STR_TO_DATE("'.$d2.' 23:59:59", "%Y-%m-%d %H:%i:%s")))';
      }


   return $r;
}
function MakeSearch($sql,$tmpl)
{
  $r='<table class="search-res">';
  $R = mysql_query_my ($sql) or die ("Error in MakeSearch<br>".mysql_error());
  $T=mysql_fetch_array($R);
  $n=0;
  while (is_array($T))
  {
    $s=$tmpl;
    $n++;
    $s=str_replace('%n%',$n ,$s);
    if ($T['weight']!="") {$T['weight'].='т.';}    if ($T['vol']!="") {$T['vol'].='м.<sup>3</sup>';}
    foreach($T as $key=>$x)
    {
      if ($x=='0000-00-00') {$x='';}      $s=str_replace('%'.$key.'%',$x ,$s);
    }
    $r.=$s;
    $T=mysql_fetch_array($R);
  }

  $r.='</table>';
  return $r;
}
function GenerateCityCND ($table, $id_city,$id_area,$id_contry)
{
  $r='';
  if ($id_city>0) //Поиск по городу
  {
    $r='(t_cargos_'.$table.'.id_city='.$id_city.')';  } elseif($id_area>0) // Поиск по региону
    {      $r='(t_cargos_'.$table.'.id_region='.$id_area.')';
    } elseif($id_contry>0) // Поиск по стране
      {      	 $r='(t_cargos_'.$table.'.id_country='.$id_contry.')';
      }
  return $r;
}
function GenerateMassaCND($table,$mass1,$mass2)
{  $r='';
  if ($mass1>0) {$r=$table.'.weight>='.$mass1;}
  if ($mass2>0) {  	if ($r!='') {$r.=' and ';}  	$r.=$table.'.weight<='.$mass2;
   }
  if ($r!='') {$r='('.$r.')';}
  return $r;
}
function GenerateVolCND ($table,$vol1,$vol2)
{  $r='';
  if ($vol1>0) {$r=$table.'.vol>='.$vol1;}
  if ($vol2>0) {
  	if ($r!='') {$r.=' and ';}
  	$r.=$table.'.vol<='.$vol2;
   }
  if ($r!='') {$r='('.$r.')';}
  return $r;
}
function GenerateTransportTypeCND ($table,$ttype)
{  $r='';
  if ($ttype>0) {$r=$table.'.type='.$ttype;}

  return $r;
}
function CargoSearch ($par)
{
  $id_cityFrom=-1;$id_cityTo=-1;
  $id_areaFrom=-1;$id_areaTo=-1;
  $id_contryFrom=-1;$id_contryTo=-1;

  $id_cityFrom=GetPostGetParamINT("cityfrom",-1);
  $id_cityTo=GetPostGetParamINT("cityto",-1);
  $id_areaFrom=GetPostGetParamINT("areafrom",-1);
  $id_areaTo=GetPostGetParamINT("areato",-1);
  $id_contryFrom=GetPostGetParamINT("contryfrom",-1);
  $id_contryTo=GetPostGetParamINT("contryto",-1);

  $dateFrom=GetPostGetParamSTR("from","");
  $dateTo=GetPostGetParamSTR("to","");

  $type=GetPostGetParamINT("type",1);

  $mass1=GetPostGetParamINT("mass1",-1);
  $mass2=GetPostGetParamINT("mass2",-1);

  $vol1=GetPostGetParamINT("vol1",-1);
  $vol2=GetPostGetParamINT("vol2",-1);
  $ttype=GetPostGetParamINT("ttype",0);

  $r='<form name="" action="CargoSearch" method="GET">';
  $r.='<input name="do" type="hidden" value="S">';
  $r.='<table class="search-form">';
  $r.='<tr><td style="border-bottom:0px;" colspan=6>'.GenereateTypeHTML($type).'</td></tr>';
  $r.='<tr><td style="width:205px;border-bottom:0px;" valign="top">'.GenerateFromToHTML($id_cityFrom,$id_areaFrom,$id_contryFrom).'</td><td style="width:30px;text-align:center;border-bottom:0px;">=></td><td style="width:205px;border-bottom:0px;" valign="top">'.GenerateFromToHTML($id_cityTo,$id_areaTo,$id_contryTo,false).'</td>';

  $r.='<td style="width:100px;border-bottom:0px;" valign="top">'.GenerateDatasHTML($dateFrom,$dateTo).'</td>';
  $r.='<td style="width:100px;border-bottom:0px;" valign="top">'.GenerateMassHTML($mass1,$mass2).'</td>';
  $r.='<td style="width:100px;border-bottom:0px;" valign="top">'.GenerateValueHTML($vol1,$vol2).'</td>';

  $r.='</tr>';
  $r.='<tr><td style="border-bottom:0px;" colspan="3"></td><td style="border-bottom:0px;" colspan="2"><b>Тип транспорта: </b>'.GenerateTransportTypeHTML($ttype).'</td></tr>';
  $r.='<tr><td style="border-bottom:0px;" colspan="6"><center><input type="submit" value="Поиск"></center></td></tr>';
  $r.='</table></form>';
  $do=false;
  $sql='';
  $tmpl='';

  if (GetPostGetParamSTR('do')=='S')
    {
      if ($type==1) {$table='t_cargos';} else {$table='t_transports';}
      $dcnd=GenerateDateCND ($table, $dateFrom, $dateTo);      $ccnd1=GenerateCityCND ('out', $id_cityFrom,$id_areaFrom,$id_contryFrom);
      $ccnd2=GenerateCityCND ('in', $id_cityTo,$id_areaTo,$id_contryTo);
      $mcnd=GenerateMassaCND ($table,$mass1,$mass2);
      $vcnd=GenerateVolCND ($table,$vol1,$vol2);
      $tcnd=GenerateTransportTypeCND ($table,$ttype);
      if ($type==1) { // Собираем sql для грузов      	 $sql='select DISTINCTROW t_cargos.id, DATE_FORMAT(t_cargos.dt,"%d/%m/%Y") as dt, t_cargos.cargo,DATE_FORMAT(t_cargos.dt1,"%d/%m") as dt1,DATE_FORMAT(t_cargos.dt2,"%d/%m") as dt2,t_cargos.addftxt,t_transptype.transport,t_cargos.weight,t_cargos.vol,FORMAT(t_cargos.distance/1000,0) as dis, t_cargos.pricetxt from t_cargos
      	 inner join t_transptype on t_transptype.id=t_cargos.type
      	 inner join t_cargos_in on t_cargos_in.id_cargo=t_cargos.id
      	 inner join t_cargos_out on t_cargos_out.id_cargo=t_cargos.id
      	 where ((1=1)';
      	 if ($dcnd!="") {$sql.=' and '.$dcnd;$do=true;}
      	 if ($ccnd1!="") {$sql.=' and '.$ccnd1;$do=true;}
      	 if ($ccnd2!="") {$sql.=' and '.$ccnd2;$do=true;}
      	 if ($mcnd!="") {$sql.=' and '.$mcnd;$do=true;}
      	 if ($vcnd!="") {$sql.=' and '.$vcnd;$do=true;}
      	 if ($tcnd!="") {$sql.=' and '.$tcnd;$do=true;}
      	 $sql.=') order by dt desc';
         $tmpl='<tr><td class="search-dt"><span class="search-citys">%n%.</span> %dt1%-%dt2%</td><td class="search-citys">{DELLA-CITYS cargos %id%}</td><td class="search-cargo">~ <b>%dis%км.</td><td class="search-cargo">%transport%</td><td class="search-cargo">%weight%</td><td class="search-cargo">%vol%</td></tr>
         <tr><td class="search-cargoadd">Разм.%dt%</td><td class="search-cargo" colspan="3">%cargo%,<br><span class="search-cargoadd">%addftxt%</span></td><td colspan="2" class="search-cargo">%pricetxt%<div style="text-align:right;"><a href="cargo-map_%id%" target="_BLANK">Открыть</a></div></td>
         </tr><tr><td colspan="6"><hr style="margin:0;border-top: 2px dotted #777;"></td></tr>';
      	}
      if ($type==2) { // Собираем sql для транспорта
      	 $sql='select DISTINCTROW t_transports.id, DATE_FORMAT(t_transports.dt,"%d/%m/%Y") as dt, DATE_FORMAT(t_transports.dt1,"%d/%m") as dt1,DATE_FORMAT(t_transports.dt2,"%d/%m") as dt2,t_transports.addftxt,t_transptype.transport,t_transports.weight,t_transports.vol,FORMAT(t_transports.distance/1000,0) as dis, t_transports.pricetxt from t_transports
      	 inner join t_transptype on t_transptype.id=t_transports.type
      	 inner join t_cargos_in on t_cargos_in.id_cargo=t_transports.id
      	 inner join t_cargos_out on t_cargos_out.id_cargo=t_transports.id
      	 where ((1=1)';
      	 if ($dcnd!="") {$sql.=' and '.$dcnd;$do=true;}
      	 if ($ccnd1!="") {$sql.=' and '.$ccnd1;$do=true;}
      	 if ($ccnd2!="") {$sql.=' and '.$ccnd2;$do=true;}
      	 if ($mcnd!="") {$sql.=' and '.$mcnd;$do=true;}
      	 if ($vcnd!="") {$sql.=' and '.$vcnd;$do=true;}
      	 if ($tcnd!="") {$sql.=' and '.$tcnd;$do=true;}
      	 $sql.=') order by dt desc';
         $tmpl='<tr><td class="search-dt"><span class="search-citys">%n%.</span> %dt1%-%dt2%</td><td class="search-citys">{DELLA-CITYS cargos %id%}</td><td class="search-cargo">~ <b>%dis%км.</td><td class="search-cargo"></td><td class="search-cargo">%weight%</td><td class="search-cargo">%vol%</td></tr>
         <tr><td class="search-cargoadd">Разм.%dt%</td><td class="search-cargo" colspan="3">%transport%,<br><span class="search-cargoadd">%addftxt%</span></td><td colspan="2" class="search-cargo">%pricetxt%<div style="text-align:right;"><a href="transport-map_%id%" target="_BLANK">Открыть</a></div></td>
         </tr><tr><td colspan="6"><hr style="margin:0;border-top: 2px dotted #777;"></td></tr>';
      	}


      if ($do) {$r.=MakeSearch($sql,$tmpl);}
        else {$r.=doNotice('Укажите условия поиска','warning');}
      //$r.='<pre>'.$sql.'</pre>';
    }
  return $r;
}

?>