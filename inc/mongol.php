<?php

function MongolEXECex($src)
{
  $res='';eval($src);

}
function MongolEXEC($src)
{
 include_once('functions.php');
 //$r.="$src<br>";
 $s=GetIn ($src, "{mgl ", "mgl}", false, false);
 while ($s!="") {
  $res=MongolEXECex($s);
  $src=ChangeIn ($src, "{mgl ", "mgl}", false, true, $res);
  //$r.=$i."$src<br>";
  $s=GetIn ($src, "{mgl ", "mgl}", false, false);
  if ($i>11)  {$src.='<br><b>Stack overflow</b>';break;}
 }
 return $src;//.'<hr>'.$r;
}

?>