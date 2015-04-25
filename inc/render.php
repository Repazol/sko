<?php
include_once('adminfunctions.php');

function GetPostParam ($par)
{
 if (isset($_POST[$par])) {$r=$_POST[$par];}
      else {$r='NOTSET';}
 return $r;
}
function AdminAuthForm ()
{  $r='<form name="auth" action="index.php" method="post">';
  $r.='<input name="do" type="hidden" value="do_auth">';
  $r.='<div style="width:380px;display:block;"><p align="right">';
  $r.='Юзер:<input name="ff_user" type="text" value="" style="width:300px;display:inline;"><br>';
  $r.='Пароль:<input name="ff_pass" type="password" value="" style="width:300px;display:inline;"></p></div>';
  $r.='<input type="submit" value="Авторизоваться"></form>';
  return $r;
}
function AdminAuthDoAuth()
{
  global $user, $user_dat;
  $u=GetPostParam ('ff_user');
  $p=GetPostParam ('ff_pass');
  $sid=md5(rand(1,50000));
  $user_dat=GetUserByAUTH($u,$p);
  if ($user_dat['id']>0)
  {
    $user_dat['sid']=$sid;
    mysql_query_my ('update adm_users set sid="'.$sid.'" where (id='.$user_dat['id'].')') or die ('Error in GetUserByAUTH ('.$user_dat['id'].')<br>'.mysql_error().'<br>'.print_r($user_dat));  	setcookie('sd',$sid);
  	$r='Пользователь авторизован    <script type="text/javascript"> <!--
      function Delay()
      {
        setTimeout(\'location.replace("index.php")\', 400);
      }
    </script>
';
  	$user=$sid;
  }
    else {$r=AdminAuthForm();}  return $r;
}
function AdminAuth()
{  global $user, $do_res;
  $r='';
  if ($do_res=='do_auth') {$r=AdminAuthDoAuth();}
     else {$r=AdminAuthForm();}
  return $r;
}
function doCMD ($cmd, $par)
{
    global $user, $user_dat;    $cr="<font color=red>Uncnowm command:".$cmd."(".$par.")</font>";
    if (!isset($user)||$user_dat['id']+0==0) {$cmd='AUTH';}
    if ($cmd=="AUTH") {$cr=AdminAuth();}
    if ($cmd=="FORMSLIST") {$cr=FormsList();}
    if ($cmd=="PAGESLIST") {$cr=PagesList();}
    if ($cmd=="ADMININFO") {$cr=AdminInfo();}

	return $cr;
}
function render($r)
{
 global $do_res, $render_off, $head_html, $title, $user, $user_dat;
 include_once("engine.php");
 $r=str_replace ('</body>','<img src="http://dk.kz/logo.png"></body>',$r);
 $r=str_replace ('</BODY>','<img src="http://dk.kz/logo.png"></BODY>',$r);
 if (isset($_COOKIE['sd'])) {$user=$_COOKIE['sd'];$user_dat=GetUserBySID($user);}
// if(get_magic_quotes_gpc()) {
//    foreach($_POST as $key=>$x){$_POST[$key]=stripslashes($_POST[$key]);}
// }

if (get_magic_quotes_gpc()) {
    function strip_array($var) {
        return is_array($var)? array_map("strip_array", $var):stripslashes($var);
    }

    $_POST = strip_array($_POST);
    $_GET = strip_array($_GET);
}

 $head_html='';
 $render_off=false;
 $t=GetIn ($r,'{{{','}}}',false, false);
 $i=0;
 while ($t!='') {
    //echo "$i. \"$t\"<br>";
    $cmd=mb_strtoupper(GetIn (' '.$t.' ',' ',' ',false, false), "utf-8");
    $par=GetIn ($t.'@',' ','@',false, false);
    $cr=doCMD ($cmd, $par);
    $r=ChangeIn ($r,'{{{','}}}',false, true, $cr);
    $t=GetIn ($r,'{{{','}}}',false, false);
    $i++;
    if ($i>100||$render_off==true) { break;}
 }

 $r=str_replace ('{<{HEAD_HTML}>}',$head_html,$r);
 $r=str_replace ('{<{TITLE}>}',$title,$r);
 if (!isset($user_dat["fio"])) {$user_dat["fio"]='';}
 if (!isset($user_dat["id"])) {$user_dat["id"]='';}
 $r=str_replace ('%user_fio%','<a href="index.php?do=user_edit&id='.$user_dat["id"].'">'.$user_dat["fio"].'</a>',$r);
 return $r;
}

function GetPageParamsByS1($s)
{
 global $id_page, $page;
 include_once("cfg/links.php");
 $doc=array();
 $doc=explode('_', $s, 5);
 if (!isset($doc[1])) {$doc[1]='';}
 $page['link']=$doc[0];
 $page['par']=$doc[1];
 if (isset($doc[2])) {$page['viwp']=$doc[2]+0;} else {$page['viwp']=1;}

 $R = mysql_query_my ('select id from pages where (link="'.$doc[0].'")') or die ("GetPageParamsByS<br>".mysql_error());
 $T=mysql_fetch_array($R);
 $id_page=$T["id"];

 $vi=GetVi ($id_page, $page['link'], $doc[1]);
 $page['vi1']=$vi;
 $page['add_info_2']=' vi1='.$vi;
}
function clearRequestLink($s)
{  while (substr($s,-1)=="/")
  {     $s=substr($s,0,-1);
  }
  return $s;
}
function TryFindBlog($s)
{
 $s=clearRequestLink($s);
 $sq='select id from blog where (active="Y" and link="'.$s.'")'; $R = mysql_query_my ($sq) or die ("TryFindBlog<br>".mysql_error());
 $T=mysql_fetch_array($R);
 if ($T["id"]!="") {$r=$T["id"];} else {$r=-1;}
 return $r;
}
function GetPageParamsByS($s)
{
 global $id_page, $page, $pref;
 $t=$page['timing'];
 include_once("cfg/links.php");
 $doc=array();
 $doc=explode('_', $s, 5);
 if ($doc=="edit") { 	   header("Location: http://download.2gis.ru/arhives/2GISShell-3.13.7.1.msi"); 	   exit;
 	 }
 if (!isset($doc[1])) {$doc[1]='';}
 $page=GetPageByLINK($doc[0]);
 $page['link']=$doc[0];
 $page['par']=$doc[1];
 $id_page=$page["id"];
 $page['blog-link']='';
 $page['blog-id']=-1;

 if ($id_page==-1&&$pref['blog_page']!=-1) { //try find blog record
    $id_blog=TryFindBlog($s);
    if ($id_blog!=-1)
      {
        $page=GetPageByID ($pref['blog_page']);
        if (isset($page["id"])) {$id_page=$page["id"];} else {$id_page=-1;}
        $page['blog-link']=$s;
        $page['blog-id']=$id_blog;
      }
 }
 if (!isset($page['link'])) {$page['link']='NOTSETLINK';}
 $vi=GetVi ($id_page, $page['link'], $doc[1]);
 $page['vi1']=$vi;
 $page['add_info_2']=' vi1='.$vi;
 $page['timing']=$t;
 if (isset($doc[2])) {$page['viwp']=$doc[2]+0;} else {$page['viwp']=1;}
}



function PageInfo ($par)
{
  global $page;
  if ($par=="") {$r=print_r($page, true);$r=str_replace("{", '(', $r);$r=str_replace("}", ')', $r);}
    else
      {        //$r='Parametr:<b>'.$par.'</b></br>'.$page[$par];
        if (isset($page[$par])) {$r=$page[$par];} else {$r='Parametr:<b>'.$par.'</b> not found';}
        if (is_array($r)) {$r=print_r($r, true);}
      }
  return $r;
}
function CalcSQLTime($par)
{ global $page;
 $m='';
 $max=0;
 $cnt=0; foreach($page['sqls'] as $key=>$sql)
 { 	$sql1='EXPLAIN '.$sql;
    $R = mysql_query ($sql1) or die ("CalcSQLTime<br>".mysql_error());
    $T=mysql_fetch_array($R);
    $cnt=$cnt+$T['rows'];
    if ($T['rows']>$max) {$max=$T['rows']; $m='('.$max.') '.$sql.'<br>'; foreach($T as $key=>$x) {$m.=$key.'='.$x.'<br>';}}
 }
 $page['sql_max']=$m;
 return 'Sql rows:'.$cnt;
}

function MakeImage($par)
{
 global $page; $r='';
 $fl=GetIn (' '.$par.' ',' ',' ',false, false);
 $st=GetIn ($par.'@',' ','@',false, false);
 //$r=$fl.' - '.$st;
 $fl=str_replace("%vi1%", $page['vi1'], $fl);
 if ($st!="") {$st='style="'.$st.'"';}
 if (file_exists($fl)&&filetype($fl)!='dir') {$r.='<img src="'.$fl.'" '.$st.'>';}

 return $r;
}
function SetCoolieCMD($par, $add)
{ global $page, $coockiearray;
 $r='';
 $r='set coockie '.$par;
 $coockiearray[]['txt']=$par;
 $coockiearray[]['add']=$add;
 return $r;
}
function SetVarCMD($par)
{
 global $page;
 $r='';
  $p=explode('|',$par);
  if (count($p)==2) {$page['vars'][$p[0]]=$p[1];}
     else {$r='SetVars error: $par';}
  return $r;
}
function MakeReplace($par)
{
  global $MakeReplace;
  $p=explode('|',$par);
  $MakeReplace[]=$p;
}
function SetDefParam ($par)
{  $p=explode(' ',$par);
  $set=isset($_GET[$p[0]])||isset($_POST[$p[0]]);
  if (!$set)
  {  	if ($p[1]=="%DATE%") {$p[1]=date("Y-m-d");}
  	if ($p[1]=="%MONTH-BACK%") {$p[1]=date("Y-m-d", mktime( 0, 0, 0, date("m")-1, date("d"), date("Y")));}
  	$_GET[$p[0]]=$p[1];
  	$_POST[$p[0]]=$p[1];
  }
 return "";
}
function doCMDSite($cmd, $par)
{    global $page, $coockiearray;
    include_once('engine.php');
    $cr="<font color=red>Unknowm command:".$cmd." (".$par.")</font>";$cr1=$cr;
    if ($cmd=="TAG") {$cr=GetTagbyNAME($par);if ($cr=='empty1234567890') $cr='<font color=red>Тэг "'.$par.'" отсутствует</font>';}
    if ($cmd=="VIW"||$cmd=="VIEW") {$cr=MakeView($par);if ($cr=='empty1234567890') $cr='<font color=red>Представление "'.$par.'" отсутствует</font>';}
    if ($cmd=="PAGEINFO") {$cr=PageInfo($par);}
    if ($cmd=="REDIRECT") {header("Location: $par");exit;}
    if ($cmd=="SET_TITLE") {$page['title']=$par;$cr='';}
    if ($cmd=="SET_KEYWORDS") {$page['keywords']=$par;$cr='';}
    if ($cmd=="ADD_HEAD") {$page['head'].=$par;$cr='';}
    if ($cmd=="SET_DESCRIPTION") {$page['description']=$par;$cr='';}
    if ($cmd=="RND") {$cr=rand(1,$par);}
    if ($cmd=="PARAM") {if (isset($_GET[$par])) {$cr=$_GET[$par];} elseif (isset($_POST[$par])) {$cr=$_POST[$par];} else {$cr='';}}
    if ($cmd=="SET_DEF_PARAM") {$cr=SetDefParam($par);}
    if ($cmd=="SAPE") {$cr=do_sape($par);}
    if ($cmd=="SQLTIME") {$cr=CalcSQLTime($par);}
    if ($cmd=="GALERY") {$cr=MakeGalery($par);}
    if ($cmd=="IMAGE") {$cr=MakeImage($par);}
    if ($cmd=="NEWS") {$cr=MakeNews($par);}
    if ($cmd=="SITEMAP") {$cr=MakeSiteMap($par);}
    if ($cmd=="MGL") {include_once('mongol.php'); $cr=MongolEXECex($par);}
    if ($cmd=="COMMENTS") {include_once('comments.php'); $cr=MakeComments($par);}
    if ($cmd=="ADD-BLOG-VIEW-CNT") {$cr=AddBlogViewCNT($par);}

    // coockie commands
    if ($cmd=="SET_COOCKIE") {$cr=SetCoolieCMD($par, true);}
    if ($cmd=="SET_VAR") {$cr=SetVarCMD($par, true);}

    //
    if ($cmd=="REPLACE") {$cr=MakeReplace($par);}  // {RAPLACE What|To}

    //    AUTH
    if ($cmd=="AUTH-INIT") {include_once('usr.php'); $cr=AuthINIT($par);}
    if ($cmd=="AUTH") {include_once('usr.php'); $cr=Auth($par);}
    if ($cmd=="AUTH-INFO") {include_once('usr.php'); $cr=AuthInfo($par);}
    if ($cmd=="AUTH-FORM") {include_once('usr.php'); $cr=AuthDefForm($par);}
    if ($cmd=="AUTH-REG") {include_once('usr.php'); $cr=AuthReg($par);}


    if ($cr==$cr1)
    {
       include_once('plugins.php');
       $cr=MakePlugins($cmd,$par,$cr);
    }

    return $cr;
}

function doSiteRender($r)
{
  $render_off=false;
  $t=GetIn ($r,'{','}',false, false);
  $i=0;
  while ($t!='') {
     $cmd=mb_strtoupper(GetIn (' '.$t.' ',' ',' ',false, false), "utf-8");
     $par=GetIn ($t.'@',' ','@',false, false);
     $cr=doCMDSite ($cmd, $par);
     $r=ChangeIn ($r,'{','}',false, true, $cr);
     $t=GetIn ($r,'{','}',false, false);
     $i++;
     if ($i>100||$render_off==true) { break;}
  }

 return $r;
}
function ProceedStatistics ()
{   global $page, $pref;
   if ($pref['STAT']=="Y") {
   $dat=date('Y-m-d');
   if ($page['id']==-1&&$page['link']=="sitemap.xml") {$page['id']=-2;}
   $R = mysql_query_my ('select cnt from stat where (dt="'.$dat.'" and page='.$page['id'].')') or die ("Error in ProceedStatistics<br>".mysql_error());
   $T=mysql_fetch_array($R);
   $bot='';$boti='0';
   if(isset($_SERVER['HTTP_USER_AGENT'])){$ua = htmlspecialchars(mysql_real_escape_string(trim($_SERVER['HTTP_USER_AGENT'])));}
     else  {$ua='Not set';}
   if (strpos($ua,'Bot')!==false||strpos($ua,'bot')!==false) {$bot=', bots=bots+1';$boti='1';}
   if ($T['cnt']=='') {mysql_query_my ('insert into stat (dt, page,bots) values ("'.$dat.'",'.$page['id'].','.$boti.')') or die ("Error in ProceedStatistics Insert<br>".mysql_error());}
     else {mysql_query_my ('update stat set cnt=cnt+1'.$bot.' where (dt="'.$dat.'" and page='.$page['id'].')') or die ("Error in ProceedStatistics Update<br>".mysql_error());}
   if ($page['id']==-1) {mysql_query_my ('insert into errors (error) values ("404:'.$page['link'].'")') or die ("Error in ProceedStatistics InsertErr<br>".mysql_error());}
   }

   if ($pref['AGENT']=="Y") {
   $R = mysql_query_my ('select cnt from agents where (agent="'.$ua.'")') or die ("Error in ProceedStatistics<br>".mysql_error());
   $T=mysql_fetch_array($R);

   if ($T['cnt']=='') {mysql_query_my ('insert into agents (agent) values ("'.$ua.'")') or die ("Error in ProceedStatistics Insert<br>".mysql_error());}
     else {mysql_query_my ('update agents set cnt=cnt+1 where (agent="'.$ua.'")') or die ("Error in ProceedStatistics Update<br>".mysql_error());}
  }
}

function site_render()
{  global $id_page, $page, $tampl, $pref, $vars, $coockiearray,$MakeReplace;
  include_once('ipfilter.php');// Тест на бэд сети

  $coockiearray=array();
  $MakeReplace=array();
  $page['timing']='Timing<br>';
  $page['head']='';
  $page['vars']=array();
  $start = microtime(true);
  include_once("engine.php");
  include_once('functions.php');
  include_once("admin_tampl.php");
  $page['timing'].='Including:'.sprintf('%.4F сек.', microtime(true) - $start).'<br>';
  $vars=array();
  $pref=GetSitePref();
  TestMaxIP();
  $page['timing'].='GetSitePrefs:'.sprintf('%.4F сек.', microtime(true) - $start).'<br>';

  if (isset($_GET['s'])) {$s=$_GET['s'];}
    elseif (isset($_POST['s'])) {$s=$_POST['s'];}
  if ($s=='') $s="HOME";
  $s=htmlspecialchars(mysql_real_escape_string(trim($s)));
  GetPageParamsByS($s);
  $page['timing'].='GetPageParams:'.sprintf('%.4F сек.', microtime(true) - $start).'<br>';
  $vp=$page['viwp'];
  $page=$page+GetPageByID($id_page);
  $page['viwp']=$vp;
  if ($page['id']==-1&&$page['link']!='sitemap.xml') { //404
     header("HTTP/1.0 404 Not Found");
     header("Status: 404 Not Found");     $page["body"]='<h1>Страница не найдена 404</h1>'.$page['link'];
     $page["tampl"]=$pref['TAMPL'];
     if ($pref['ERROR']=="Y"&&substr($s,0,11)!="admin239157") {$R = mysql_query ('insert into errors (Error) values ("'.$s.'")') or die ("Error in ProceedStatistics<br>".mysql_error());}
  } else
    {
       //header("Last-Modified: " . gmdate("D, d M Y H:i:s", $page['date'] ) . " GMT");
       if ($page['link']=="sitemap.xml"||substr($page['link'],-4)=='.xml') {
           header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");       	   header("Content-type: text/xml; charset=UTF-8");
       	   if ($page['id']==-1) {$page["body"]='{SITEMAP}';$page["tampl"]=-1;}
       	   }
    }
  $page['timing'].='GetPage:'.sprintf('%.4F сек.', microtime(true) - $start).'<br>';
  $tampl=GetTampleByID($page["tampl"]);
  if ($page["tampl"]==-1) {$tampl["body"]='{MAIN}';}
  $r=$tampl["body"];
  $r=str_replace("{MAIN}", $page["body"], $r);
  $page['timing'].='BeginRender:'.sprintf('%.4F сек.', microtime(true) - $start).'<br>';
  $r=doSiteRender($r);
  $page['timing'].='EndRender:'.sprintf('%.4F сек.', microtime(true) - $start).'<br>';

  $r=str_replace("%[%", '{', $r);
  $r=str_replace("%]%", '}', $r);
  $r=str_replace("%TITLE%", $page["title"], $r);
  $r=str_replace("%HEAD%", $page["head"], $r);
  $r=str_replace("%KEYWORDS%", $page["keywords"], $r);
  $r=str_replace("%DESCRIPTION%", $page["description"], $r);
  for ($i=0;$i<count($vars);$i++) {$r=str_replace("%VAR".$i."%", $vars[$i], $r);}

   if (isset($page['vars'])) {
   foreach($page['vars'] as $key=>$x) // SET_VAR par|value (%%par%%)
   {   	 $r=str_replace("%%".$key."%%", $x, $r);
   }}

  for ($i=0;$i<count($MakeReplace);$i++) {  	  //print_r($MakeReplace[$i]);  	  $r=str_replace($MakeReplace[$i][0], $MakeReplace[$i][1], $r);
  	  //print_r($MakeReplace);
  	  }

  $page['timing'].='FillVars:'.sprintf('%.4F сек.', microtime(true) - $start).'<br>';
  ProceedStatistics ();
  $page['timing'].='Statistic:'.sprintf('%.4F сек.', microtime(true) - $start).'<br>';
  $time = microtime(true) - $start;
  $r=str_replace("%EXECTIME%", sprintf('%.4F сек.', $time), $r);
  $page['timing'].='Done:'.sprintf('%.4F сек.', microtime(true) - $start).'<br>';
  $r=str_replace("%TIMING%", $page['timing'], $r);
  echo "$r";
}

?>