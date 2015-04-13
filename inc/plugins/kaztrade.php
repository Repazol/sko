<?php

function MakeKaztradeMain($par)
{
  include_once("engine.php");
  $r='<script src="js/moris/raphael-min.js"></script><script src="js/moris/morris.min.js"></script>';
  $dt1=GetPostGetParam('dt1');
  $dt2=GetPostGetParam('dt2');
  $dtCND='(dt>="'.$dt1.'" and dt<=STR_TO_DATE("'.$dt2.' 23:59:59", "%Y-%m-%d %H:%i:%s"))';
  //Собираем Даты
  $dats=array();
  $sql='select date(dt) as dat from log where ('.$dtCND.') group by dat';
  $R = mysql_query_my ($sql) or die ("Error in MakeKaztradeMain<br>".mysql_error());
  //$r.='Dates:'.$sql.'<br>';
  $T=mysql_fetch_array($R);
    while (is_array($T))
    {
     $dats[$T['dat']]=array();
     $dats[$T['dat']]['dat']=$T['dat'];
     for ($i=1;$i<4;$i++) {$dats[$T['dat']][$i]=0;}
 	 $T=mysql_fetch_array($R);
    }
  //Собираем оценки
  foreach($dats as $key=>$x)
  {    $sql='select reason, count(reason) as cnt from log where (DATE(dt)="'.$key.'" and reason>0) group by reason';
    $R = mysql_query_my ($sql) or die ("Error in MakeKaztradeMain<br>".mysql_error());
    $T=mysql_fetch_array($R);
      while (is_array($T))
       {
         $dats[$key][$T['reason']]=$T['cnt'];
     	 $T=mysql_fetch_array($R);
       }
  }

  $r.='<div id="line-example" style="width:90%;"></div>';

  $r.='<script>Morris.Line(%[% element: \'line-example\',';
  $r.='data: [';
  foreach($dats as $key=>$x)
  {  	$r.='%[% y: \''.$key.'\', a: '.$x[1].', b: '.$x[2].' , c: '.$x[3].' %]%,';
  }
  $r.='],';
  $r.='xkey: \'y\', xLabels: \'day\', ykeys: [\'a\', \'b\', \'c\'],  lineColors: [\'green\',\'blue\',\'red\'],labels: [\'Отлично\', \'Хорошо\', \'Плохо\']%]%);</script>';


  //$r.='<pre>'.print_r($dats,true).'</pre>';

  //$r.=$dtCND;

  return $r;
}

function PluginRender ($cmd,$par,$cr)
{ if ($cmd=="KAZTRADE-MAIN") {$cr=MakeKaztradeMain($par);}
 return $cr;
}

?>