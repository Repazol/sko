<?php

function MongolEXECex($src)
{
  $res='';eval($src);  return $res;

}
function MongolEXEC($src)
{ $r='';$i=0;
 include_once('functions.php');
 //$r.="$src<br>";
 $s=GetIn ($src, "{mgl ", "mgl}", false, false);
 while ($s!="") {  $i++;
  $res=MongolEXECex($s);
  $src=ChangeIn ($src, "{mgl ", "mgl}", false, true, $res);
  //$r.=$i."$src<br>";
  $s=GetIn ($src, "{mgl ", "mgl}", false, false);
  if ($i>11)  {$src.='<br><b>Stack overflow</b>';break;}
 }
 return $src;//.'<hr>'.$r;
}

?>