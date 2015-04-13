<?php

function MakeNewsEx($par) // par -
{ $r='';
 $p=explode(' ',$par);
 if (isset($p[0])) {$limit=$p[0];} else {$limit='10';}
 if (isset($p[1])) { 	include_once('engine.php');
 	$pat=GetTagbyNAME($p[1]);$i=0;
 	} else { 		$pat='<p><sup>%d%</sup> <b>%title%</b><br>%img%%note%<br></p><br>';$i=1;
 		}

 $R = mysql_query ('select news.id, news.note, news.img, news.d, news.title, news.uid , blog_cats.name from news left join blog_cats on blog_cats.id=news.id_cat order by news.d desc limit '.$limit) or die ("Error in News<br>".mysql_error());
 $T=mysql_fetch_array($R);
 while (is_array($T))
  {
     if ($T['img']=='') {$T['img']='';} else {if ($i==1) $T['img']='<img src="'.$T['img'].'" width="100" align="left">';}
     $v=$pat;
     foreach($T as $key=>$x) {$v=str_replace('%'.$key.'%', $x, $v);}
     $v=str_replace('%title%', 'TITLT', $v);
     $r.=$v;
 	 $T=mysql_fetch_array($R);
  }
 return $r;
}

?>