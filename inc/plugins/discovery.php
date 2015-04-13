<?php

function MakeDiscoveryFilter($par)
{
  global $page;
  $r='';
  if ($page['user']['id']==-1) {$r='{TAG '.$par.'}';}
  return $r;
}
function MakeDiscoveryInfo($par)
{
  global $page;
  $r='';
  if ($page['user']['id']==-1) {$r='<div style="color:green;font-size:14pt;"> <a href="reg">Зарегистрируйтесь, чтобы избавиться от рекламы</a></div>';}
  return $r;
}
function PluginRender ($cmd,$par,$cr)
{
 if ($cmd=="DISCOVERY-FILTER") {$cr=MakeDiscoveryFilter($par);}
 if ($cmd=="DISCOVERY-INFO") {$cr=MakeDiscoveryInfo($par);}
 return $cr;
}


?>