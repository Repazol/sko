<?php
function doErr ($s)
{  $R = mysql_query ('insert into errors (Error) values ("'.$s.'")') or die ("Error in doErr<br>".mysql_error());
  exit;
}

function TestMaxIP()
{
global $pref;
if ($pref['MAX_IP']!='0')
  {
  $hosts=array();
  $hosts[]='mail.ru';$hosts[]='googlebot.com';$hosts[]='msn.com';$hosts[]='yandex';$hosts[]='google';


  $put=true;
  $ip=getClientIP();
  $R = mysql_query ('select ip, allow,cnt from ipslog where (ip="'.$ip.'")') or die ("Error in TestMaxIP<br>".mysql_error());
  $T=mysql_fetch_array($R);
  if (!isset($T['ip']))
  {    $url=gethostbyaddr($ip);
    $a='N';
    foreach($hosts as $key=>$x)
    {      if (strpos($url,$x)>0) {$a='Y';}
    }
    $sql='insert into ipslog (ip,allow, host) values ("'.$ip.'", "'.$a.'", "'.$url.'")';
  }
    else
      {
        $put=$T['allow']<>'Y';
        $sql='update ipslog set cnt=cnt+1 where (ip="'.$ip.'")';
      }
  if ($put) {
     $R = mysql_query ($sql) or die ("Error in TestMaxIP<br>".mysql_error().'<br>'.$sql);
  }

}

}

function net_match($network, $ip)
{
      $ip_arr = explode('/', $network);
      $network_long = ip2long($ip_arr[0]);
      $x = ip2long($ip_arr[1]);
      $mask =  long2ip($x) == $ip_arr[1] ? $x : 0xffffffff << (32 - $ip_arr[1]);
      $ip_long = ip2long($ip);
      return ($ip_long & $mask) == ($network_long & $mask);
}


  $ip=$_SERVER['REMOTE_ADDR'];
  $ips=array();
  $ips[]='91.224.182.0/23@NTK Route Obj';
  $ips[]='109.207.0.0/20@OJSC Rostelecom';
  $ips[]='178.237.206.0/24@FGUP NTC ATLAS';
  $ips[]='78.237.240.0/20@Moscow City Radio Broadcasting Network';
  $ips[]='193.105.14.0/24@RTTC-net';
  $ips[]='193.27.214.0/23@Network Federal agency on education';
  $ips[]='193.47.146.0/24@State Research Institute of Aviation Systems';
  $ips[]='194.150.202.0/23@Scientific Network';
  $ips[]='194.165.22.0/23@RU-GRFC-NET';
  $ips[]='194.190.224.0/19@RUNNet';
  $ips[]='194.190.89.0/24@Federal State Unity Enterprise';
  $ips[]='194.226.22.0/23@State Science Centre VNIIGeosystem';
  $ips[]='194.226.80.0/20@RGIN';
  $ips[]='194.226.116.0/22@RSNET';
  $ips[]='194.226.127.0/24@Main Division of Informations Resources';
  $ips[]='194.226.192.0/19@RUNNet';
  $ips[]='194.85.160.0/20@RUNNet';
  $ips[]='194.85.30.0/24@Research Centre for Computer Science at the Russian Foreign Ministry';
  $ips[]='194.85.32.0/20@RUNNet';
  $ips[]='195.149.110.0/24@ATLAS-NW-NET';
  $ips[]='195.208.224.0/19@RUNNet';
  $ips[]='80.250.160.0/19@RUNNet';
  $ips[]='82.137.128.0/18@RUNNet';
  $ips[]='82.179.0.0/16@RUNNet';
  $ips[]='85.142.0.0/15@RUNNet';
  $ips[]='85.142.52.0/24@RUNNet';
  $ips[]='91.190.236.0/22@Novosibirsk department of Atlas ISP';
  $ips[]='91.227.32.0/24@FGUP Goznak';
  $ips[]='91.236.22.0/23@VNIIGeosystem';
  $ips[]='94.199.64.0/21@ATOMLINK-AS';
  $ips[]='95.173.128.0/19@The Federal Guard Service of the Russian Federation';
  $ips[]='213.24.76.0/23@RTCOMM-RU';
  $ips[]='92.39.133.160/28@The Investigative Committee';
  $ips[]='109.207.13.0/24@Electronic-government';

  foreach($ips as $dat)
  {    $ip_d=explode ('@',$dat);
    if (net_match($ip_d[0], $ip)) {doErr ('IPF:'.$ip_d[1]);}
  }



?>