<?php

include_once('inc/cfg/dbconnect.php');
include_once('inc/engine.php');
$co=connect();
$d=GetPostGetParamINT("d");
$id_quest=GetPostGetParamINT("q");
if ($d!=-1&&$id_quest!=-1)
{  $r=rand(100,150);

  $ans=array();
  $sql='select id_answ from sko_main where (id_quest='.$id_quest.') group by id_answ';
  $R = mysql_query_my ($sql) or die ("Error in SkoMakeQuestStatistic<br>".mysql_error());
  $T=mysql_fetch_array($R);
    while (is_array($T))
    {      $ans[]=$T['id_answ'];
 	  $T=mysql_fetch_array($R);
    }
  $pls=array();
  $sql='select id_place from sko_main where (id_quest='.$id_quest.') group by id_place';
  $R = mysql_query_my ($sql) or die ("Error in SkoMakeQuestStatistic<br>".mysql_error());
  $T=mysql_fetch_array($R);
    while (is_array($T))
    {
      $pls[]=$T['id_place'];
 	  $T=mysql_fetch_array($R);
    }
  var_dump($pls);
  var_dump($ans);
  srand((float)microtime() * 1000000);
  for ($i=0;$i<$r;$i++)
  {     //$dt=date("d-m-Y", mktime( 0, 0, 0, date("M"), date("d")-rand(0,$d), date("Y")));
     $da = new DateTime(date("d-m-Y"));
     $da->modify('-'.rand(0,$d).' day');
     $dt=$da->format("Y-m-d");
     shuffle($ans);
     shuffle($pls);

     $id_a=$ans[0];
     $id_p=$pls[0];

     $sql='insert into sko_main(dt,id_quest,id_answ,id_place) values ("'.$dt.'",'.$id_quest.','.$id_a.','.$id_p.')';
     echo $sql.'<br>';
     mysql_query_my ($sql) or die ("Error in SkoMakeQuestStatistic<br>".mysql_error());



  }


}


mysql_close($co);

?>