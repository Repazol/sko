<?php

function DueCampingFunctions($cmd,$par,$cr)
{
  if ($cmd=="SITE_AUTH") {
  	  include_once("siteauth.php");
  	  $cr=SiteAuth($par);
  	}

  if ($cmd=="CAM-REGISTRATION") {
  	  include_once("campingregistr.php");
  	  $cr=doCampReg($par);
  	}

 return $cr;
}

?>