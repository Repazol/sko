<?php
function MakeGalType1($gal)
{
 global $page;
 $style=file_get_contents('galerys/data/gal1_style.dat');
 $pr=$gal['name'];
 $style=str_replace("%pref%", $pr, $style);
 $style=str_replace("%f_width%", $gal['width']+$gal['m_width']+2*$gal['border'], $style);
 $style=str_replace("%mb_width%", $gal['m_width']+2*$gal['border'], $style);
 $style=str_replace("%percent%", $gal['i_width']/$gal['m_width'], $style);

 foreach($gal as $key=>$x) {$style=str_replace("%".$key."%", $x, $style);}
 $page['head'].=$style;
 $r='<div class="'.$pr.'hovergallery" style="width:'.$gal['width'].';height:'.$gal['height'].'">';
 $dir='galerys/'.$gal['cat'].'/';
 $n=0;
 if ($dirh = opendir($dir)) {
    while (($file = readdir($dirh)) !== false) {
      if (filetype($dir . $file)!='dir')
        {
         //$r.=$file.'<br>';
         $n++;
         $p=$n%$gal['cols'];

         $img='<img src="'.$dir.$file.'" alt="'.$n.'" / width="'.$gal['m_width'].'" height="'.$gal['m_height'].'">';
         if ($gal['link']!='')
         {
           $link=$gal['link'];
           $link=str_replace("%src%", $file, $link);
           $img='<a href="'.$link.'">'.$img.'</a>';
         }
         $r.=$img;
         if ($p==0) {$r.='<br>';}
        }
    }
  closedir($dirh);
  }

 $r.='</div>';
 return $r;
}
function MakeGalType2($gal)
{ global $page;
 $style=file_get_contents('galerys/data/gal2_style.dat');
 $pr=$gal['name'];
 $style=str_replace("%pref%", $pr, $style);
 $style=str_replace("%f_width%", $gal['width']+$gal['m_width']+2*$gal['border'], $style);
 $style=str_replace("%mb_width%", $gal['m_width']+2*$gal['border'], $style);
 foreach($gal as $key=>$x) {$style=str_replace("%".$key."%", $x, $style);}
 $page['head'].=$style;

 $dir='galerys/'.$gal['cat'].'/';
 $r='<div id="'.$pr.'gallery2">';
 $r1='<ul id="'.$pr.'tabs2">';
 $r2='<div id="'.$pr.'fullPicBlock">';
 $n=0;
 if ($dirh = opendir($dir)) {
    while (($file = readdir($dirh)) !== false) {
      if (filetype($dir . $file)!='dir')
        {         //$r.=$file.'<br>';
         $n++;
         $r1.='<li><a href="#'.$pr.'pic'.$n.'"><img src="'.$dir.$file.'" alt="" / width="'.$gal['m_width'].'" height="'.$gal['m_height'].'"></a></li>';
         $r2.='<div><a name="'.$pr.'pic'.$n.'"></a><img src="'.$dir.$file.'" alt="" width="'.$gal['i_width'].'" height="'.$gal['i_height'].'"/></div>';
        }
    }
  closedir($dirh);
  }
 $r1.='</ul>';
 $r2.='</ul>';
 $r.=$r1.$r2.'</div>';
 return $r;
}

function MakeGalType3($gal)
{
 global $page;
 $style=file_get_contents('galerys/data/gal3_style.dat');
 $pr=$gal['name'];
 $style=str_replace("%pref%", $pr, $style);
 $style=str_replace("%f_width%", $gal['width']+$gal['m_width']+2*$gal['border'], $style);
 $style=str_replace("%mb_width%", $gal['m_width']+2*$gal['border'], $style);

 $add='';
 foreach($gal as $key=>$x) {$style=str_replace("%".$key."%", $x, $style);}

 $dir='galerys/'.$gal['cat'].'/';
 $r='<div style="width:'.$gal['width'].';height:'.$gal['height'].'">';
 $r.='<ul class="'.$pr.'gallery">';
 $n=0;
 if ($dirh = opendir($dir)) {
    while (($file = readdir($dirh)) !== false) {
      if (filetype($dir . $file)!='dir')
        {
         //$r.=$file.'<br>';
         $n++;
         $ang=rand(-30,30);
         $add.='ul.'.$pr.'gallery li a.pic-'.$n.' {
		z-index: '.$n.';
		-webkit-transform: rotate('.$ang.'deg);
 		-moz-transform: rotate('.$ang.'deg);
	}
          ';
         $link='#';
         if ($gal['link']!='')
         {           $link=$gal['link'];
           $link=str_replace("%src%", $file, $link);
         }
         $r.='<li><a href="'.$link.'" class="pic-'.$n.'"><img src="'.$dir . $file.'" width="'.$gal['m_width'].'" height="'.$gal['m_height'].'"/></a></li>';
         if ($n%$gal['cols']==0) {$r.='<br>';}
        }
    }
  closedir($dirh);
  }
 $style=str_replace("%add_list%", $add, $style);
 $page['head'].=$style;
 $r.='</ul>';
 return $r;

}

function MakeGalType4($gal)
{
 global $page;
 // galerys\data\js\
 $page['head'].='	<link rel="stylesheet" href="galerys/data/lb/css/screen.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="galerys/data/lb/css/lightbox.css" type="text/css" media="screen" />
 ';

 $dir='galerys/'.$gal['cat'].'/';

 $r='<div class="imageRow">';
 $n=0;
 if ($dirh = opendir($dir)) {
    while (($file = readdir($dirh)) !== false) {
      if (filetype($dir . $file)!='dir')
        {
         //$r.=$file.'<br>';
         $n++;
         //$r.='<li><a href="'.$link.'" class="pic-'.$n.'"><img src="'.$dir . $file.'" width="'.$gal['m_width'].'" height="'.$gal['m_height'].'"/></a></li>';
		 $r.='  	<div class="single">
  		<a href="'.$dir . $file.'" rel="lightbox[plants]"><img src="'.$dir . $file.'" width="'.$gal['m_width'].'" height="'.$gal['m_height'].'" alt="" /></a>
  	</div>
';
         //if ($n%$gal['cols']==0) {$r.='<br>';}
        }
    }
  closedir($dirh);
  }
 $r.='</div>';
 $r.='<script src="galerys/data/lb/js/jquery-1.7.2.min.js"></script>
<script src="galerys/data/lb/js/jquery-ui-1.8.18.custom.min.js"></script>
<script src="galerys/data/lb/js/jquery.smooth-scroll.min.js"></script>
<script src="galerys/data/lb/js/lightbox.js"></script>
';

 return $r;
}
function GenerateGalery($gal)
{ $r='';
 if ($gal['type']==1) {$r=MakeGalType1($gal);}
 if ($gal['type']==2) {$r=MakeGalType2($gal);}
 if ($gal['type']==3) {$r=MakeGalType3($gal);}
 if ($gal['type']==4) {$r=MakeGalType4($gal);}

 return $r;
}

?>