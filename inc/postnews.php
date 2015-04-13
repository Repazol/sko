<?php // PostNews
function GetNewsIDByUID($uid)
{ $R = mysql_query ('select id from news where (uid="'.$uid.'")') or die ("Error in News 1<br>".mysql_error());
 $T=mysql_fetch_array($R);
 if (is_array($T)) {$id=$T['id'];} else {$id=-1;}
 return $id;
}
function GetCatIDByName($cat)
{ $R = mysql_query ('select id from blog_cats where (name="'.$cat.'" and type=2)') or die ("Error in News 2<br>");
 $T=mysql_fetch_array($R);
 if (is_array($T)) {$id=$T['id'];} else {$id=-1;}
 if ($id==-1)
 {    $R = mysql_query ('insert into blog_cats (name, type) values ("'.$cat.'", 2)') or die ("Error in News 3<br>");
    $R = mysql_query ('select LAST_INSERT_ID() as id from blog_cats') or die ("Error in News 4<br>");
    $T=mysql_fetch_array($R);
    if (is_array($T)) {$id=$T['id'];} else {$id=-1;}
 }
 return $id;
}
  //echo "Starting...<br>";
  $do_w=false;
  if (isset($_POST['do'])&&$_POST['do']=='news')
  {
  include_once('cfg/dbconnect.php');
  $co=connect();
  foreach($_POST as $key=>$x){$_POST[$key]=mysql_real_escape_string($x);}

  if ($_POST['link']=="") // Generate SEO link by title
  {
    include_once("functions.php");
  	$_POST['link']=encodestring($_POST['title']);
  }
  $id=GetNewsIDByUID($_POST['link']);
  $dt=$_POST['dt'];
  //$dt=substr($d,-4).'-'.substr($d,3,2).'-'.substr($d,0,2);
  $img=$_POST['img'];
  $nt=$_POST['note'];

  $id_cat=GetCatIDByName($_POST['cat']);
  $_POST['cat']+0;
  if ($id==-1) {$sq='insert into news (d, id_cat, title, note, uid, body, img) values ("'.$dt.'",'.$id_cat.',"'.$_POST['title'].'","'.$nt.'","'.$_POST['link'].'","'.$_POST['body'].'","'.$img.'")';}
    else {$sq='update news set d="'.$dt.'", img="'.$img.'", note="'.$nt.'", title="'.$_POST['title'].'", body="'.$_POST['body'].'", uid="'.$_POST['link'].'", id_cat='.$id_cat.' where (id='.$id.')';}
  mysql_query ($sq) or die ("Error in News 5<br>");
  mysql_close($co);
  echo "OK";
  $do_w=true;
  }

  if ($do_w==false) {echo "Mysql error";}
?>