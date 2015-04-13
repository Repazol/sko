<?php

function PluginRender($cmd,$par,$cr)
{
 global $page;
 $auth=$page['user']['id']!=-1;
 $cr='<font color="red">Функция:<b>'.$cmd.'</b> не найдена</font>';
 $auth_f=array('SKO-FILIALS','SKO-QUESTS','SKO-QUESTIONS','SKO-DESIGN','SKO-STATISTIC');
 if (in_array($cmd, $auth_f)&&$auth) {
 if ($cmd=="SKO-FILIALS"&&$auth) {include_once("sko-filials.php"); $cr=SkoFilials($par);}
 if ($cmd=="SKO-QUESTS"&&$auth) {include_once("sko-quests.php"); $cr=SkoQuests($par);}
 if ($cmd=="SKO-QUESTIONS"&&$auth) {include_once("sko-questions.php"); $cr=SkoQuestions($par);}
 if ($cmd=="SKO-DESIGN"&&$auth) {include_once("sko-design.php"); $cr=SkoDesign($par);}
 if ($cmd=="SKO-STATISTIC"&&$auth) {include_once("sko-stat.php"); $cr=SkoStatistic($par);}
 } else
   {     $cr='<h2>Необходимо авторизоваться</h2>%%SHDL%%';
     $page['vars']['SHDL']='<script>
       $(function() {       	setTimeout(function(){
            $("#opener").trigger("click");
            },1000)       });
       </script>
     ';
   }

 if ($cmd=="SKO-GETQUESTINFO") {include_once("sko-quiestinfo.php"); $cr=SkoGetQuestInfo($par);}
 if ($cmd=="SKO-PUTINFO") {include_once("sko-putinfo.php"); $cr=SkoPutInfo($par);}



 return $cr;//.'<pre>'.print_r($page['user'],true).'</pre>';
}


?>