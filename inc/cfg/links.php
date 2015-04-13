<?php

function GetVi ($p, $link, $s)
{
  global $page;
  $sql='';
  $vi=-1;
  if ($s!='') $vi=$s;
  $page['add_info']='id_page='.$p.' link='.$link.' s='.$s;
  if ($link='update') {$vi=$s;}
  if ($link='gal') {$vi=$s;}
  $page['add_info'].=' vi1='.$vi;
  return $vi;
}


?>