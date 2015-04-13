<?php

function MakePlugins($cmd,$par, $cr)
{  $p=explode('-',$cmd);
  if (count($p)==2)
  {
     $p[0]=mb_strtolower($p[0]);
     $fn=dirname(__FILE__).'/plugins/'.$p[0].'.php';
     clearstatcache();
     if (file_exists($fn))
      {
        include_once($fn);
        $cr=PluginRender($cmd,$par,$cr);
      }
        else {$cr.='<br>Plugin <b>'.$p[0].'</b> not found';}

  }

  return $cr;
}

?>