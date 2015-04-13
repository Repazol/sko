<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<title>Документальные фильмы онлайн &raquo; Страница 2</title>
</head><body>
<?php

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
function GetBlogId ($l)
{  $R = mysql_query ('select id from blog where (link="'.$l.'")') or die ("Error in put<br>".mysql_error());
  $T=mysql_fetch_array($R);
  if (is_array($T)) {$id=$T['id'];} else $id=-1;
  return $id;
}
function put ($a, $tit, $img, $txt)
{  $dt=date("Y-m-d H:i:s");
  $title=addslashes($tit);
  $body=addslashes($txt);
  include_once("../../inc/functions.php");
  $link=encodestring($tit);

  if (GetBlogId ($link)==-1)
   {
     $sql='insert into blog (dt,active,title,body,img, link) values ("'.$dt.'","Y","'.$title.'","'.$body.'","'.$img.'","'.$link.'")';
     $R = mysql_query ($sql) or die ("Error in put<br>".mysql_error());
     $id=GetBlogId ($link);
     if ($id!=-1) {     	$R = mysql_query ('insert into blog_data (id_blog,id_cat) values ('.$id.',5)') or die ("Error in put<br>".mysql_error());
     	//tweet ('http://vidiks.ru/video_'.$T['id'], $tit, $img);     }
   }

}
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

$start = microtime(true);
//echo "Start<br>";
include_once('../../inc/cfg/dbconnect.php');
include 'simple_html_dom.php';
$co=connect();
$h = file_get_html('http://cluclu.ru/blog/');
$links=array();
 foreach($h->find('h3 a') as $a){ 	$links[]=$a->href;
  }

 foreach(array_reverse($links) as $a){
 	echo $a.'<br>';
 	$h = file_get_html($a);
 	$e=$h->find('h1',0);
 	$title=$e->plaintext;
 	$e=$h->find('div.topic-content img',0);
 	$img=$e->src;
    echo "$title $img <br><img src=\"$img\"><br>";
 	$e=$h->find('div.topic-content',0);
 	$txt=$e->innertext;
 	$txt=str_replace('<a ','<fhu ',$txt);
    //echo "$txt<hr>";
    $e = $h->find( "meta[name=keywords]" );
    $keyw=$e[0]->content;
    $e = $h->find( "meta[name=description]" );
    $ds=$e[0]->content;
    $txt.='{SET_KEYWORDS '.$keyw.'}';
    $txt.='{SET_DESCRIPTION '.$ds.'}';
    put ($a, $title, $img, $txt);
    $time = microtime(true) - $start;
    if ($time>20) break;
  }

mysql_close($co);
$time = microtime(true) - $start;
printf('Скрипт выполнялся %.4F сек.', $time);

?>
</body>

</html>