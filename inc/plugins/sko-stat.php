<?php
function TT()
{  $r='

  <script type="text/javascript">
  window.onload = function () {
    var chart = new CanvasJS.Chart("chartContainer", {      chart:{      	creditText: "Подробнее..",
      	creditHref: "other"
      },
      title:{
        text: "Fruits sold in First Quarter"
      },
      data: [
        {
         type: "pie",
         dataPoints: [
         { label: "Плохо", y: 18 },
         { label: "Хорошо", y: 29 },
         { label: "apple", y: 40 },
         { label: "mango", y: 34 },
         { label: "grape", y: 24 }
         ]
       }
       ]
     });
    chart.render();
  }
  </script>
';
return $r;
};
function Myhtmlspecialchars($s)
{ $s=str_replace('"','`',$s);
 $s=str_replace("'",'`',$s);
 return $s;
}
function DoData ($name, $div, $type,$data)
{  if (substr($data,0,1)==",") {$data=substr($data,1);}
  $r='
  <script type="text/javascript">
   document.addEventListener("DOMContentLoaded",function(){
    var c'.$div.' = new CanvasJS.Chart("'.$div.'", {
      title:{
        text: "'.Myhtmlspecialchars($name).'",
        fontSize: 20
      },
      data: [
        {
         type: "'.$type.'",
         dataPoints: ['.$data.'
         ]
       }
       ]
     });
    c'.$div.'.render();
  });
  </script>
';
return $r;
}
function DoDataMulti ($name, $div, $data)
{
  if (substr($data,0,1)==",") {$data=substr($data,1);}
  $r='
  <script type="text/javascript">
   document.addEventListener("DOMContentLoaded",function(){
    var c'.$div.' = new CanvasJS.Chart("'.$div.'", {
      title:{
        text: "'.Myhtmlspecialchars($name).'",
        legend: {
       horizontalAlign: "left", // "center" , "right"
       verticalAlign: "bottom",  // "top" , "bottom"
       fontSize: 15
     },
        fontSize: 20
      },
      data: [
         '.$data.'
       ]
     });
    c'.$div.'.render();
  });
  </script>
';
return $r;
}

function DoData1 ($labels,$series,$div,$type,$name, $legend)
{
if (substr($labels,0,1)==",") {$labels=substr($labels,1);}if (substr($series,0,1)==",") {$series=substr($series,1);}

$r="

<script>
  document.addEventListener('DOMContentLoaded',function(){    var data = {
    labels: [$labels],
    series: [$series]

};
var responsiveOptions = [
  ['screen and (min-width: 640px)', {
    chartPadding: 20,
    labelOffset: 100,
    labelDirection: 'explode',
    labelInterpolationFnc: function(value) {
      return value;
    }
  }],
  ['screen and (min-width: 1024px)', {
    labelOffset: 50,
    chartPadding: 20
  }]
];
var options = {
  width: 550,
  height: 300,
  labelInterpolationFnc: function(value) {
    return value[0]
  }
  };
new Chartist.$type('#$div', data,options,responsiveOptions);
});
</script>

".'<table style="width:auto;"><tr><td><div class="ct-chart" id="'.$div.'"></div></td><td valign="top"><b>'.$name.'</b>'.$legend.'</td></tr></table>';;
return $r;//ct-chart ct-golden-section
}
function MakeRoundQuestions ($quest)
{
  global $page,$startdate,$showcnt;
  $r='';
  $columns=3;
  $v='q'.$quest['id'];
  $page['vars']['gr'.$v]='';;
  $r.='<table>';
  $n=0;
  foreach($quest['questions'] as $q)
  {
  $data='';
  $sql='select sko_answers.txt,count(sko_main.id_answ) as cnt from sko_main inner join sko_answers on sko_answers.id=sko_main.id_answ where (sko_main.id_quest='.$q['id'].' and '.$startdate.') group by sko_main.id_answ order by cnt';
  $R = mysql_query_my ($sql) or die ("Error in SkoStatistic<br>".mysql_error().'<br>'.$sql);
  $T=mysql_fetch_array($R);
    while (is_array($T))
    {
      if ($showcnt) {$T['txt'].=' ('.$T['cnt'].')';}      $data.=','."\n".'{ label: "'.$T['txt'].'", y: '.$T['cnt'].' }';

 	  $T=mysql_fetch_array($R);
    }
  if ($data!="") {     if ($n==0) {$r.='<tr>';}
     $page['vars']['gr'.$v].=DoData ($q['name'], 'pie'.$q['id'], 'pie',$data);
     $r.='<td style="text-align:right;"><div id="'.'pie'.$q['id'].'" style="height: 300px; width: 400px; background:#eeeeee;"></div><a href="statistics_'.$q['id'].'">Подробнее...</a></td>';
     $n++;
     if ($n==$columns) {$n=0;$r.='</tr>';}
   }
  }
  if ($n!=$columns) {$r.='</tr>';}

  $r.='</table>%%gr'.$v.'%% ';
  return $r;
}
function MakeLinesQuestions ($quest)
{  global $page,$startdate,$showcnt;
  $r='';
  $columns=2;
  $v='q'.$quest['id'];
  $page['vars']['grl'.$v]='';
  $r.='<table>';
  $n=0;
  $nn=0;
  foreach($quest['questions'] as $q)
  {
  $data='';
  $sql='select DATE_FORMAT(sko_main.dt,"%d-%m-%Y") as dt, sko_answers.id as aid, sko_answers.txt,count(sko_main.id_answ) as cnt from sko_main
inner join sko_answers on sko_answers.id=sko_main.id_answ
where (sko_main.id_quest='.$q['id'].' and '.$startdate.')
group by DATE_FORMAT(sko_main.dt,"%d-%m-%Y"), sko_answers.id
order by sko_main.dt';
  $ax=array();
  $n=1;
  $dt='';
  $R = mysql_query_my ($sql) or die ("Error in SkoStatistic<br>".mysql_error());
  $T=mysql_fetch_array($R);
    while (is_array($T))
    {
      if ($dt=="") {$dt=$T['dt'];}
      if ($dt!=$T['dt']) {$n++;}
      $f=false;
      if (!isset($ax[$T['aid']])) {$ax[$T['aid']]='
        type: "line",
        legendText: "'.$T['txt'].'",
        showInLegend: true,
        dataPoints: [';
        $f=true;}
      if (!$f) {$ax[$T['aid']].=',';}
      $ax[$T['aid']].="\n".'{ label: "'.$T['dt'].'",x:'.$n.', y: '.$T['cnt'].' }';
      //$data.=','."\n".'{ label: "'.$T['txt'].'", y: '.$T['cnt'].' }';
      $dt=$T['dt'];
 	  $T=mysql_fetch_array($R);
    }
  $data='';
  foreach($ax as $d)
  {  	$data.=',{'.$d.']}';
  }
  if ($data!="") {
     if ($nn==0) {$r.='<tr>';}
     $page['vars']['grl'.$v].=DoDataMulti ($q['name'], 'line'.$q['id'], $data);
     $r.='<td><div id="'.'line'.$q['id'].'" style="height: 300px; width: 600px; background:#eeeeee;"></div></td>';
     $nn++;
     if ($nn==$columns) {$n=0;$r.='</tr>';}
   }
  }
  if ($nn!=$columns) {$r.='</tr>';}

  $r.='</table>%%grl'.$v.'%% ';
  return $r;
}
function SkoMakeMianStatistic($idu)
{  global $page;
  $r='';

  $quests=array();
  $sql='select id,name,UNIX_TIMESTAMP(cod) as cod, `change` from sko_quests where (id_user='.$idu.' and is_del="N") order by name';
  $R = mysql_query_my ($sql) or die ("Error in SkoStatistic<br>".mysql_error());
  $T=mysql_fetch_array($R);
    while (is_array($T))
    {
      $q=array();
      $q['id']=$T['id'];
      $q['name']=$T['name'];
      $q['ch']=$T['change'];
      $quests[]=$q;
 	  $T=mysql_fetch_array($R);
    }
    foreach($quests as $quest)
    {
       $r.='<h2>'.$quest['name'].'</h2>';
       $sql='select id,question,pos from sko_questions where (id_q='.$quest['id'].') order by pos';
       $R = mysql_query_my ($sql) or die ("Error in SkoStatistic<br>".mysql_error());
       $questions=array();
       $T=mysql_fetch_array($R);
         while (is_array($T))
         {
           $q=array();
           $q['id']=$T['id'];
           $q['name']=$T['question'];
           $questions[]=$q;
      	  $T=mysql_fetch_array($R);
         }
       $quest['questions']=$questions;

       $r.=MakeRoundQuestions ($quest);
       $r.=MakeLinesQuestions ($quest);
    }

  return $r;
}
function SkoMakeQuestStatistic($idu,$id_q)
{  global $page,$startdate,$showcnt;
  $r='';
  $sql='select id,question from sko_questions where (id='.$id_q.' and id_user='.$idu.') order by pos';
  $R = mysql_query_my ($sql) or die ("Error in SkoMakeQuestStatistic<br>".mysql_error());
  $T=mysql_fetch_array($R);
  if (is_array($T))
  {  	$r.='<h2>'.$T['question'].'</h2>';
  	// By Places
  	$r.='<h3>По местам</h3>';
    $sql='select sko_places.name, sko_answers.txt, count(sko_main.dt) as cnt,sko_filials.name as filial from sko_main
          inner join sko_places on sko_main.id_place=sko_places.id
          inner join sko_filials on sko_places.id_filial=sko_filials.id
          inner join sko_answers on sko_main.id_answ=sko_answers.id
          where (sko_main.id_quest='.$id_q.' and '.$startdate.')
          group by sko_places.id_filial, sko_places.id, sko_answers.id
          order by sko_filials.name,sko_places.name';
    $R = mysql_query_my ($sql) or die ("Error in SkoMakeQuestStatistic<br>".mysql_error());
    $T=mysql_fetch_array($R);
    $q=array();
    while (is_array($T))
    {
      $key=$T['name'].' '.$T['filial'];
      if (!isset($q[$key])) {$q[$key]='';}
      if ($showcnt) {$T['txt'].='('.$T['cnt'].')';}
      $q[$key].=','."\n".'{ label: "'.htmlspecialchars($T['txt']).'", y: '.$T['cnt'].' }';
 	  $T=mysql_fetch_array($R);
    }
    $columns=4;
    $g='<table>';
    $n=0;$i=0;
    foreach($q as $data=>$place)
    {
       $i++;       if ($n==0) {$g.='<tr>';}
       $g.='<td><div id="re'.$i.'" style="height: 300px; width: 400px; background:#eeeeee;"></div>'.DoData ($data,'re'.$i,'pie',$place).'</td>';
       $n++;
       if ($n==$columns) {$g.='</tr>';$n=0;}
    }
   if ($n!=$columns) {$g.='</tr>';}
   $g.='</table>';
   $page['vars']['qt1']=$g;
   $r.='%%qt1%%';

  	// By Answers
  	$r.='<h3>По ответам</h3>';
    $sql='select sko_answers.txt, sko_places.name,count(sko_main.dt) as cnt,sko_filials.name as filial from sko_main
          inner join sko_places on sko_main.id_place=sko_places.id
          inner join sko_filials on sko_places.id_filial=sko_filials.id
          inner join sko_answers on sko_main.id_answ=sko_answers.id
          where (sko_main.id_quest='.$id_q.' and '.$startdate.')
          group by sko_places.id_filial,sko_places.id, sko_answers.id
          order by sko_answers.txt, cnt desc';
    $R = mysql_query_my ($sql) or die ("Error in SkoMakeQuestStatistic<br>".mysql_error());
    $T=mysql_fetch_array($R);
    $q=array();
    while (is_array($T))
    {
      if (!isset($q[$T['txt']])) {$q[$T['txt']]='';}
      $lb=Myhtmlspecialchars($T['name'].' '.$T['filial']);
      if ($showcnt) {$lb.=' ('.$T['cnt'].')';}
      $q[$T['txt']].=','."\n".'{ label: "'.$lb.'", y: '.$T['cnt'].' }';
 	  $T=mysql_fetch_array($R);
    }
    $columns=4;
    $g='<table>';
    $n=0;$i=0;
    foreach($q as $data=>$place)
    {
       $i++;
       if ($n==0) {$g.='<tr>';}
       $g.='<td><div id="rez'.$i.'" style="height: 300px; width: 400px; background:#eeeeee;"></div>'.DoData ($data,'rez'.$i,'pie',$place).'</td>';
       $n++;
       if ($n==$columns) {$g.='</tr>';$n=0;}
    }
   if ($n!=$columns) {$g.='</tr>';}
   $g.='</table>';
   $page['vars']['qt2']=$g;
   $r.='%%qt2%%';



  }

  return $r;
}
function MakeHeader()
{
  global $page, $startdate,$showcnt;
  $page['vars']['stat-hrader']='
    <script>
function setCookie(name, value, options) {
  options = options || {};
  var expires = options.expires;
  if (typeof expires == "number" && expires) {
    var d = new Date();
    d.setTime(d.getTime() + expires*1000);
    expires = options.expires = d;
  }
  if (expires && expires.toUTCString) {
    options.expires = expires.toUTCString();
  }
  value = encodeURIComponent(value);
  var updatedCookie = name + "=" + value;
  for(var propName in options) {
    updatedCookie += "; " + propName;
    var propValue = options[propName];
    if (propValue !== true) {
      updatedCookie += "=" + propValue;
     }
  }
  document.cookie = updatedCookie;
}
function getCookie(name) {
  var matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, "\\$1") + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}


  $(function() {
    $("#radio").buttonset();
    $(":radio").click(function() {
         //alert($(this).val());
         setCookie("period", $(this).val(), { expires: 3600*9000 });
         location.reload();
      });
    $("#show-cnt").click(function() {    	 var s = getCookie("showcnt");
    	 if (s=="on") {s="off";} else {s="on";}
    	 //alert(getCookie("showcnt")+" -> "+s);
         setCookie("showcnt", s, { expires: 3600*9000 });
         location.reload();
      });
  });
   $(function() {
    $( "#stat" )
      .button()
      .click(function( event ) {
        window.location = "statistics";
      });
  });
  </script>';
  $r='%%stat-hrader%%
   <div id="radio"><button id="stat">Статистика</button> <span style="font-size:14pt;padding-left:90px;">Период:</span>
  ';
  $pers=array('Год','Квартал','Месяц','Неделя','Вчера','Сегодня');
  if (isset($_COOKIE['period'])) {$per=$_COOKIE['period']+0;} else {$per=2;}
  foreach($pers as $i =>$p)
  {    $r.='<input type="radio" id="radio'.$i.'" name="radio" value="'.$i.'"';
    if ($i==$per) {$r.=' checked="checked"';}
    $r.='><label for="radio'.$i.'">'.$p.'</label>';
  }

  $ch='';$ss='';
  if ($showcnt) { $ch=' checked="checked"';$ss=' Показываем количество';}

  $r.='<snpan>&nbsp;&nbsp;&nbsp;&nbsp;</span><input id="show-cnt" type="checkbox"'.$ch.'><label for="show-cnt">Показывать количество</label>';
  $r.='</div>';
  $d=0;
  if ($per==0) {$d=365;}
  if ($per==1) {$d=3*30;}
  if ($per==2) {$d=31;}
  if ($per==3) {$d=7;}
  if ($per==4) {$d=1;}
  if ($per==5) {$d=0;}
  $da = new DateTime(date("d-m-Y"));
  if ($d!=0) {$da->modify('-'.$d.' day');}

  $startdate='sko_main.dt>STR_TO_DATE("'.$da->format("Y-m-d").' 00:00:00", "%Y-%m-%d %H:%i:%s")';

  return $r;
}

function SkoStatistic($par)
{
  global $page, $showcnt;
  $showcnt=false;
  if (isset($_COOKIE['showcnt'])) {
  	  if ($_COOKIE['showcnt']=="on")
  	    {
      	  $showcnt=true;
      	}
  }

  $page['head'].='<script type="text/javascript" src="gr1/canvasjs.min.js"></script>';
  $r='';
  $id_q=$page['vi1'];
  if ($id_q=="") {$id_q="-1";}
  $idu=$page['user']['id'];
  $r.=MakeHeader();
  if ($id_q==-1) {$r.=SkoMakeMianStatistic($idu);}
   else {$r.=SkoMakeQuestStatistic($idu,$id_q);}

 return $r;
}

?>