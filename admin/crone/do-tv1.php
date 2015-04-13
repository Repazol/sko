<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<title>Документальные фильмы онлайн &raquo; Страница 2</title>
<script type="text/javascript" src="jquery.js"></script>
</head><body>
<?php

function connect ()
{
 $sok=mysql_connect("localhost","repazol1_vidiks", "monster") or die (mysql_error());
 mysql_query("SET NAMES 'utf8'");
 mysql_select_db("repazol1_vidiks",$sok) or die ('DB:'.mysql_error());
 return $sok;
}

function tweet ($a, $tit, $img)
{
require_once('codebird.php');
 
\Codebird\Codebird::setConsumerKey("uLRte7gckzc5vPWWRvdyDdKfj", "kgSEIdn2jfTWj60ChTSp7JYOuYDKOfwWwwv6qDWKRmKpAApnnh");
$cb = \Codebird\Codebird::getInstance();
$cb->setToken("861103537-3IpwWRrsADfkOWJp6AUbqYup0UoONntYnB2b0mcA", "77HZutnS7SKLDTUx9tYZulL7VwsWlyUhZA7mRx1aisNGI");
 
$params = array(
  'status' => $tit.' '.$a,
  'media[]' => $img
);
$reply = $cb->statuses_updateWithMedia($params);
echo "Tweet...<br>";
echo "<pre>";
var_dump ($reply);
echo "</pre>";

}
function put ($a, $tit, $img, $txt,$keyw,$desc)
{
  $p1=addslashes($tit);
  $p2=$a;
  $i=strpos($txt,'<img ');
  $txt[$i+1]='g';
  $p3=addslashes('<img src="'.$img.'"><hr>'.$txt);
  $p4='Y';
  $p5=date("Y-m-d H:i:s");
  $p6=addslashes($keyw);
  $p7=addslashes($desc);

  $R = mysql_query ('select id from t1 where (p2="'.$a.'")') or die ("Error in put sel<br>".mysql_error().'<br>'.$a);
  $T=mysql_fetch_array($R);
  if (is_array($T)) {$id=$T['id'];} else $id=-1;
  if ($id==-1)
   { $sql='insert into t1 (p1, p2, p3, p4, p5, p6, p7) values ("'.$p1.'","'.$p2.'","'.$p3.'","'.$p4.'","'.$p5.'","'.$p6.'","'.$p7.'")';}
     else  { $sql='update t1 set p1="'.$p1.'", p2="'.$p2.'",p3="'.$p3.'",p4="'.$p4.'",p5="'.$p5.'",p6="'.$p6.'",p7="'.$p7.'" where (id='.$id.')';}
  $R = mysql_query ($sql) or die ("Error in put 1<br>".mysql_error().'<br>'.$sql);
  echo $tit."<br>\n";
  if ($id==-1)
  {
  $R = mysql_query ('select id from t1 where (p2="'.$a.'")') or die ("Error in put<br>".mysql_error());
  $T=mysql_fetch_array($R);
  $R = mysql_query ('insert into t4 (p1,p2) values ('.$T['id'].',1165)') or die ("Error in put<br>".mysql_error());
  //if (is_array($T)) {tweet ('http://vidiks.ru/video_'.$T['id'], $tit, $img);}
    
  
  }
}
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

$start = microtime(true);
//echo "Start<br>";
include 'simple_html_dom.php';
//include_once ('functions.php');
$co=connect();
$h = file_get_html('http://tv1.kz/filmyonlayn');
$links=array();
 foreach($h->find('h2 a') as $a){ 	$links[]=$a->href;
  }

 foreach(array_reverse($links) as $a){
 	echo $a.'<br>';
 	$h = file_get_html($a);
 	$e=$h->find('h1',0);
 	$title=$e->plaintext;
	//$title = mb_convert_encoding($title, "windows-1251","utf-8");
 	$e=$h->find('div.full-news-image img',0);
 	$img='http://tv1.kz/'.$e->src;
    echo "$title<br>$img <br><img src=\"$img\"><hr>";
 	$e=$h->find('div.full-news-content',0);
 	$txt=$e->innertext;
	
	$ln='';
    foreach($h->find('span[style="color:#FFFF99"]') as $aa){
 	  $ln.='<div id="video">'.$aa->plaintext.'</div>';
    }
	
	$i=strpos($txt,'<b>');
	$txt=substr($txt,0,$i);
	
	//$txt=GetIn ($txt, '<div class="full-news-content">', '<b>', false, false);
	$txt=iconv("CP1251", "UTF-8", $txt);

	$txt.= "<br>\n".$ln."\n";
	$txt.= '<script type="text/javascript" src="v.js"></script>';
    $txt=str_replace('<img src="/','<img src="http://tv1.kz/',$txt);
	
 	//$txt=str_replace('<a href="http://d','<fhu ',$txt);
    echo "$txt<hr>";
    $e = $h->find( "meta[name=keywords]" );
    $keyw=$e[0]->content;
    $e = $h->find( "meta[name=description]" );
    $ds=$e[0]->content;
    put ($a, $title, $img, $txt,$keyw,$ds);

    $time = microtime(true) - $start;
    if ($time>20) break;
  }

mysql_close($co);
$time = microtime(true) - $start;
printf('Скрипт выполнялся %.4F сек.', $time);

?>
</body>

</html>