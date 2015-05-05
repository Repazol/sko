<?php
function mysql_query_my ($sql)
{
  global $page;
  if (!isset ($page['sqls'])) {$page['sqls']=array();}
  $page['sqls'][]=$sql;  return mysql_query ($sql);
}

function FileManager()
{
  global $user_dat;
  if ($user_dat['acl']>=500) {	$r=' <iframe src="fm/ft2.php" width="100%" height="600" align="left">Ваш браузер не поддерживает плавающие фреймы!</iframe>';
  } else {$r='<b>Нет доступа</b>';}
	return $r;
}

function News()
{ global $user_dat;
 if ($user_dat['acl']>=1000||$user_dat['r_blogs']=='r'||$user_dat['r_blogs']=='rw') {
 if (isset($_GET['page'])) {$page=$_GET['page']+0;} else {$page=1;}
 $news_count=50;
 $limit=($page-1)*$news_count.', '.$news_count;
 $r='<b>Новости</b> &nbsp; <a href="index.php?do=news_edit&id=-1" title="Создать новую">+</a>';
 $r.='<table>';
 $r.='<tr><th width="120">Дата</th><th width="500">Заголовок</th><th>Категории</th><th>Ссылка</th><th width="20"></th></tr>';
 $R = mysql_query ('select news.id, news.d, news.title, news.uid , blog_cats.name from news left join blog_cats on blog_cats.id=news.id_cat order by news.d desc limit '.$limit) or die ("Error in News<br>".mysql_error());
 $T=mysql_fetch_array($R);
 while (is_array($T))
  {
     if (!isset($T["name"])) {$T["name"]='<font color=red>Не указанна</font>';}
     $r.='<tr><td>'.$T["d"].'</td><td><a href="index.php?do=news_edit&id='.$T["id"].'">'.$T["title"].'</a></td><td>'.$T["name"].'</td><td>'.$T["uid"].'</td><td align="center"><a href="index.php?do=news_delete&id='.$T["id"].'" title="Удалить">X</a></td></tr>';
 	 $T=mysql_fetch_array($R);
  }
 $r.='</table>';
 } else {$r='<b>Нет доступа</b></br>';}
  return $r;
}
function NewsEdit()
{  global $head_html, $do_res, $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_blogs']=='rw') {
  $r='<b>Редактирование новости</b><br>';
  include_once('engine.php');
  $news=GetNewsRecordByID ($id);
  $r.='<form action="index.php" method="post">';
  $r.='<input name="do" type="hidden" value="news_post">';
  $r.='<input name="id" type="hidden" value="'.$news["id"].'">';
  $r.='Дата: <input name="dt" style="width:150px; margin:0 0 0 0;display:inline;" value="'.$news["d"].'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  $r.='Заголовок: <input name="title" type="text" value="'.$news["title"].'" style="width:350px;margin:0 0 0 0;display:inline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';
  $r.='Категория:<select size="1" name="cat" style="margin: 5 0 5px 5;">';
  $cats=GetAllCats('blog_cats.type=2','name');
  foreach($cats as $key=>$x)
  {
    $s='';
    if ($x['id']==$news['id_cat']) {$s=' checked';}
    $r.='<option value="'.$x['id'].'"'.$s.'>'.$x['name'].'</option>';
  }
  $r.='</select>';

  $r.='<textarea id="editor1" class="ckeditor" name="body" rows=5 cols=20 wrap="off">'.$news["body"].'</textarea>';

  $r.='Ссылка:<input name="link" type="text" value="'.$news["uid"].'" style="width:400px;margin:0 0 0 0;display:inline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';
  $a='';
  $r.='Миниатюра:';
  $r.='<input style="width:350px; margin:0 0 0 0;display:inline;" type="text" id="srcFile_function" name="srcFile_function" value="'.$news["img"].'" size="50" />&nbsp;<input style="margin:0 0 0 0;display:inline;" type="button" value="Выбрать" onclick="AjexFileManager1.open({returnTo: \'insertValue\'});" /><br>';
  $r.='Краткое описание:<textarea style="height:150px; margin:0 0 0 0;" name="note" rows=5 cols=20 wrap="off">'.$news["note"].'</textarea>';
  $r.='<input type="submit" value="Сохранить"></form>';
  $ingtml=file_get_contents('ckeditor/inhtml.dat');
  $r.=$ingtml;
  $head_html='<script type="text/javascript" src="ckeditor/ckeditor.js"></script><script type="text/javascript" src="AjexFileManager/ajex.js"></script><script type="text/javascript" src="AjexFileManager/ajex1.js"></script>';
  $head_html.='<link rel=\'stylesheet\' href=\'calendar/calendar.css\' type=\'text/css\'><script type=\'text/javascript\' src=\'calendar/calendar.js\'></script>';
  } else {$r='<b>Нет доступа</b></br>';}
  return $r;
}
function NewsPost()
{  global $head_html, $do_res, $id, $user_dat;
  if ($user_dat['acl']>=1000||$user_dat['r_blogs']=='rw') {
  $r='';
  if ($_POST['link']=="") // Generate SEO link by title
  {
    include_once("functions.php");
  	$_POST['link']=encodestring($_POST['title']);
  }
  $dt=$_POST['dt'];
  //$dt=substr($d,-4).'-'.substr($d,3,2).'-'.substr($d,0,2);
  $img=$_POST['srcFile_function'];
  $nt=mysql_real_escape_string($_POST['note']);
  $id_cat=$_POST['cat']+0;
  $_POST['body']=mysql_real_escape_string($_POST['body']);
  if ($id==-1) {$sq='insert into news (d, id_cat, title, note, uid, body, img) values ("'.$dt.'",'.$id_cat.',"'.$_POST['title'].'","'.$nt.'","'.$_POST['link'].'","'.$_POST['body'].'","'.$img.'")';}
    else {$sq='update news set d="'.$dt.'", img="'.$img.'", note="'.$nt.'", title="'.$_POST['title'].'", body="'.$_POST['body'].'", uid="'.$_POST['link'].'", id_cat='.$id_cat.' where (id='.$id.')';}
  mysql_query ($sq) or die ("Error in NewsPost<br>".mysql_error());

  $r.='<div class="notice success"><i class="icon-ok icon-large"></i><b>Новость сохранена</b><a href="#close" class="icon-remove"></a></div>';
  $r.=News();
  } else {$r='<div class="notice error"><i class="icon-remove-sign icon-large"></i><b>Нет доступа</b><a href="#close" class="icon-remove"></a></div>';}
  return $r;
}
function NewsDelete()
{  global $head_html, $do_res, $id, $user_dat;
 if ($user_dat['acl']>=1000||$user_dat['r_blogs']=='rw') {
  include_once('engine.php');
  $tmpl=GetNewsRecordByID($id);
  $r='<table width="100%" border=0>';
  $r.='<tr><th align="center" colspan="2">Удалить новость:</b></br>'.$tmpl["title"].'</th></tr>';

  $r.='<tr><td width="50%" align="right">';
  $r.='<form action="index.php" method="POST">';
  $r.='<input name="do" type="hidden" value="news_del">';
  $r.='<input name="id" type="hidden" value="'.$tmpl["id"].'">';
  $r.='<input type="submit" value="Удалить"></form></td>';
  $r.='<td><form action="index.php" method="GET"><input name="do" type="hidden" value="blog"><input type="submit" value="Нет"></form></td></tr></table>';
  } else {$r='<b>Нет доступа</b></br>';}
  return $r;
}

function NewsDel()
{
  global $do_res, $id, $user_dat;
 if ($user_dat['acl']>=1000||$user_dat['r_blogs']=='rw') {
  include_once('engine.php');
  $tmpl=GetNewsRecordByID($id);
  $r='<div class="notice error"><i class="icon-remove-sign icon-large"></i>Удалено:</font>'.$tmpl["title"].'<a href="#close" class="icon-remove"></a></div>';
  mysql_query ('delete from news where (id='.$id.')') or die ("Error in NewsDel<br>".mysql_error());
  $r.=News();
  } else {$r='<div class="notice error"><i class="icon-remove-sign icon-large"></i><b>Нет доступа</b><a href="#close" class="icon-remove"></a></div>';}
  return $r;
}

function MySqlManager()
{
  include_once('databasemanager.php');
  $r=DatabaseManager();
  return $r;
}

function DatabasesManager()
{
  include_once('dbmang.php');
  $r=DBManager();
  return $r;
}

function AdminReportsList()
{  global $do_res, $id, $user_dat;
 if ($user_dat['acl']>=1) {
  include_once('engine.php');
  $r='<b>Отчеты!</b> <a href="index.php?do=admin-reports-edit?id=-1">+</a></br>';
  $R = mysql_query ('select id, name from adm_reports order by name') or die ("Error in AdminReportsList<br>".mysql_error());
  $T=mysql_fetch_array($R);
  $r.='<table width="99%">';
  while (is_array($T))
    {
     $r.='<tr><td><a href="index.php?do=admin-reportshow&id='.$T['id'].'">'.$T["name"].'</a></td><td width="50"><a href="index.php?do=admin-reports-del?id='.$T["id"].'">X</a></td></tr>';
 	 $T=mysql_fetch_array($R);
    }
  $r.='</table>';
  } else {$r='<div class="notice error"><i class="icon-remove-sign icon-large"></i><b>Нет доступа</b><a href="#close" class="icon-remove"></a></div>';}
  return $r;

}
function AdminReportShow()
{  global $do_res, $id, $user_dat;
 if ($user_dat['acl']>=1) {  error_reporting(0);
  include_once('engine.php');
  $R = mysql_query ('select id, name, viewlist from adm_reports where (id='.$id.')') or die ("Error in AdminReportShow<br>".mysql_error());
  $T=mysql_fetch_array($R);
  $cr=MakeView($T['viewlist']);if ($cr=='empty1234567890') $cr='<font color=red>Представление "'.$T['viewlist'].'" отсутствует</font>';
  $r='<b>'.$T['name'].'</b></br>'.$cr;
  } else {$r='<div class="notice error"><i class="icon-remove-sign icon-large"></i><b>Нет доступа</b><a href="#close" class="icon-remove"></a></div>';}
  return $r;
}
function AdminInfo()
{  global $do_res, $user_dat; //&&$user_dat['acl']+0>500
  include_once("admin_tampl.php");
  $r='';
  if ($do_res=='pref'||$do_res=='pref_post') $r=AdminPref ();
  //Tamples
  if ($do_res=='tampls') $r=AdminTamples ();
  if ($do_res=='tample_post') $r=AdminTamplePost ().AdminTamples ();
  if ($do_res=='tampl_edit') $r=AdminTampleEdit ();
  if ($do_res=='tampl_delete') $r=AdminTampleDelete ();
  if ($do_res=='tample_del') $r=AdminTampleDeleteOk ().AdminTamples ();
  // Pages
  if ($do_res=='pages') $r=AdminPages();
  if ($do_res=='page_edit') $r=AdminPageEdit();
  if ($do_res=='page_post') $r=AdminPagePost().AdminPages();
  if ($do_res=='page_delete') $r=AdminPageDelete();
  if ($do_res=='page_del') $r=AdminPageDel();
  // Tags
  if ($do_res=='tags') $r=AdminTags();
  if ($do_res=='tag_edit') $r=AdminTagEdit();
  if ($do_res=='tag_post') $r=AdminTagPost().AdminTags();
  if ($do_res=='tag_delete') $r=AdminTagDelete();
  if ($do_res=='tag_del') $r=AdminTagDel();
  // Views
  if ($do_res=='views') $r=AdminViews();
  if ($do_res=='view_edit') $r=AdminViewEdit();
  if ($do_res=='view_post') $r=AdminViewPost().AdminViews();
  // Galereys
  if ($do_res=='gals') $r=AdminGals();
  if ($do_res=='gals_edit') $r=AdminGalsEdit();
  if ($do_res=='gals_post') $r=AdminGalsPost().AdminGals();
  // css editors
  if ($do_res=='css') $r=AdminCSS();
  if ($do_res=='css_post') $r=AdminCSSPost();
  if ($do_res=='css_edit') $r=AdminCSSEdit();

  if ($do_res=='back'||$do_res=='back_make') $r=BackupManager();

  if ($do_res=='users') $r=UsersManager();
  if ($do_res=='user_edit') $r=UsersEdit();
  if ($do_res=='user_post') $r=UsersPost();
  // Blog Cats
  if ($do_res=='cats') $r=Cats();
  if ($do_res=='cats_edit') $r=CatsEdit();
  if ($do_res=='cats_post') $r=CatsPost();
  if ($do_res=='cats_delete') $r=CatsDelete();
  if ($do_res=='cats_del') $r=CatsDel();
  // Blog
  if ($do_res=='blog') $r=Blogs();
  if ($do_res=='blog_edit') $r=BlogEdit();
  if ($do_res=='blog_post') $r=BlogPost();
  if ($do_res=='blog_delete') $r=BlogDelete();
  if ($do_res=='blog_del') $r=BlogDel();
  // Mongol
  if ($do_res=='mongol') $r=MongolAdm();

  if ($do_res=='whatsapp'||$do_res=='whatsapp-send'||$do_res=='whatsapp-recive') {include_once('whatsapp.php'); $r=WhatsAppAdm();}


  //Auth
  if ($do_res=='auth-users') {$r=AuthUsersListManager();};
  if ($do_res=='auth-fields') {$r=AuthUsersListFields();};
  if ($do_res=='authuser_fieldedit') {$r=AuthUsersListFieldEdit();};
  if ($do_res=='authuser_fieldeditpost') {$r=AuthUsersListFieldEditPost().AuthUsersListFields();};


  //News
  if ($do_res=='news') $r=News();
  if ($do_res=='news_edit') $r=NewsEdit();
  if ($do_res=='news_post') $r=NewsPost();
  if ($do_res=='news_delete') $r=NewsDelete();
  if ($do_res=='news_del') $r=NewsDel();
  //Admin reports
  if ($do_res=='admin-reports') $r=AdminReportsList();
  if ($do_res=='admin-reportshow') $r=AdminReportShow();



  if ($do_res=='fm') $r=FileManager();

  if ($do_res=='mysql'||$do_res=='mysql-table'||$do_res=='mysql-edit') $r=MySqlManager();
  if ($do_res=='databases'||$do_res=='dbedit'||$do_res=='dbedit-rec'||$do_res=='dbedit-rec-post'||$do_res=='del-dbrec'||$do_res=='del-dbrec-ok') $r=DatabasesManager();

  if ($do_res=='clear_errors') $r=ClearTable("errors");
  if ($do_res=='clear_stat') $r=ClearTable("stat");
  if ($do_res=='clear_agents') $r=ClearTable("agents");
  if ($r=='') {$r=GetStatistic();}

  return $r;
}


?>
