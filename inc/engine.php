<?php
include_once('adminfunctions.php');
function doNotice($txt, $type='success')
{  return '<div class="notice '.$type.'"><i class="icon-remove-sign icon-large"></i>'.$txt.'<a href="#close" class="icon-remove"></a></div>';
}
function GetPostGetParam($v, $default="")
{  if (isset($_GET[$v])) {$id=$_GET[$v];}
    elseif (isset($_POST[$v])) {$id=$_POST[$v];}
       else {$id=$default;}
 return $id;
}

function GetPostGetParamINT($v,$default=-1)
{  return GetPostGetParam($v,$default)+0;
}

function GetPostGetParamSTR($v,$default="")
{
  return mysql_real_escape_string(GetPostGetParam($v,$default));
}

function GetBlogRecordByID ($id)
{  $r=array();
  $r['id']=-1;
  $r['dt']=date("d-m-Y");
  $r['title']='';
  $r['body']='';
  $r['link']='';
  $r['img']='';
  $r['note']='';
  $r['active']='N';
  $r['cats']=array();
  if ($id!=-1&&$id!='')
  {
    $R = mysql_query_my ('select * from blog where (id='.$id.')') or die ("Error in GetBlogRecordByID<br>".mysql_error());
    $T=mysql_fetch_array($R);
    $r=$T;
    $r['dt']= date("d-m-Y", strtotime($r['dt']));
    $r['cats']=array();
    $R = mysql_query_my ('select id_cat, blog_cats.name from blog_data inner join blog_cats on blog_cats.id=id_cat where (id_blog='.$id.')') or die ("Error in GetBlogRecordByID<br>".mysql_error());
    $T=mysql_fetch_array($R);
    while (is_array($T))
    {
     $r['cats'][$T['id_cat']]=$T['name'];
 	 $T=mysql_fetch_array($R);
    }
  }
  return $r;
}

function GetNewsRecordByID ($id)
{
  $r=array();
  $r['id']=-1;
  $r['d']=date("Y-m-d");
  $r['title']='';
  $r['body']='';
  $r['id_cat']=-1;
  $r['uid']='';
  $r['note']='';
  $r['img']='';
  if ($id!=-1&&$id!='')
  {
    $R = mysql_query_my ('select * from news where (id='.$id.')') or die ("Error in GetNewsRecordByID<br>".mysql_error());
    $T=mysql_fetch_array($R);
    $r=$T;
  }
  return $r;
}

function GetBlogRecordByLink ($link)
{
  $r=array();
  $r['id']=-1;
  $r['dt']=date("d-m-Y");
  $r['title']='';
  $r['body']='';
  $r['link']='';
  $r['img']='';
  $r['note']='';
  $r['active']='N';
  $r['cats']=array();
  if ($link!='')
  {
    $link=strip_tags($link);
    $R = mysql_query_my ('select * from blog where (link="'.$link.'")') or die ("Error in GetBlogRecordByLink<br>".mysql_error());
    $T=mysql_fetch_array($R);
    if (is_array($T)) {
    $r=$T;
    $r['dt']= date("d-m-Y", strtotime($r['dt']));
    $r['cats']=array();
    $R = mysql_query_my ('select id_cat, blog_cats.name from blog_data inner join blog_cats on blog_cats.id=id_cat where (id_blog='.$r['id'].')') or die ("Error in GetBlogRecordByID<br>".mysql_error());
    $T=mysql_fetch_array($R);
    while (is_array($T))
    {
     $r['cats'][$T['id_cat']]=$T['name'];
 	 $T=mysql_fetch_array($R);
    }
   }
  }
  return $r;
}
function AddBlogViewCNT($id)
{
  $id=$id+0;  $R = mysql_query_my ('update blog set cnt=cnt+1 where (id="'.$id.'")') or die ("Error in AddBlogViewCNT<br>".mysql_error());
  return "";
}

function GetCatByID($id)
{
 $r=array();
 $r["id"]=$id;
 $r["id_up"]=-1;
 $r["name"]='';
 $r["type"]=0;
 if ($id!=-1&&$id!='')
 {
    $R = mysql_query_my ('select * from blog_cats where (id='.$id.')') or die ("Error in GetCatByID ('.$id.')<br>".mysql_error());
    $T=mysql_fetch_array($R);
    $r=$T;
 }
 return $r;
}
function GetAllCats($where='',$order='')
{
 $r=array();
 $sq='select blog_cats.id, blog_cats.id_up, blog_cats.name, bc.name as cat, blog_cats.type from blog_cats left join blog_cats as bc on bc.id=blog_cats.id_up';
 if ($where!='') {$sq.=' where ('.$where.')';}
 if ($order!='') {$sq.=' order by '.$order;}
 $R = mysql_query_my ($sq) or die ("Error in GetAllCats<br>".mysql_error().'<br>'.$sq);
 $T=mysql_fetch_array($R);
  while (is_array($T))
  {
     $r[]=$T;
 	 $T=mysql_fetch_array($R);
  }
 return $r;
}

function GetUserByID($id)
{ $r=array();
 $r["id"]=$id;
 $r["user"]='';
 $r["pass"]='';
 $r["acl"]=0;
 $r["fio"]='';
 $r["rights"]=0;
 $r["sid"]='';
 $r["r_users"]='';
 $r["r_tampls"]='';
 $r["r_pages"]='';
 $r["r_tags"]='';
 $r["r_views"]='';
 $r["r_gals"]='';
 $r["r_css"]='';
 $r["r_blogs"]='';
 $r["r_backs"]='';
 $r["r_prefs"]='';
 if ($id!=-1&&$id!='')
 {
    $R = mysql_query_my ('select * from adm_users where (id='.$id.')') or die ("Error in GetUserByID ('.$id.')<br>".mysql_error());
    $T=mysql_fetch_array($R);
    $r=$T;

    $r["r_users"]='';
    $r["r_tampls"]='';
    $r["r_pages"]='';
    $r["r_tags"]='';
    $r["r_views"]='';
    $r["r_gals"]='';
    $r["r_css"]='';
    $r["r_blogs"]='';
    $r["r_backs"]='';
    $r["r_prefs"]='';
    $p=explode('&',$r['rights']);
    foreach($p as $key=>$x)
    {       $u=explode('=',$x);
       if ($u[0]!="") $r["r_".$u[0]]=$u[1];
    }
 }
 return $r;
}
function GetUserBySID($sid)
{
 $r=array();
 $r["id"]=-1;
 $r["user"]='';
 $r["pass"]='';
 $r["acl"]=0;
 $r["sid"]='';
 $r["fio"]='';
 $r["r_users"]='';
 $r["r_tampls"]='';
 $r["r_pages"]='';
 $r["r_tags"]='';
 $r["r_views"]='';
 $r["r_gals"]='';
 $r["r_css"]='';
 $r["r_blogs"]='';
 $r["r_backs"]='';
 $r["r_prefs"]='';
 if ($sid!='')
 {
    $R = mysql_query_my ('select * from adm_users where (sid="'.$sid.'")') or die ("Error in GetUserBySID ('.$id.')<br>".mysql_error());
    $T=mysql_fetch_array($R);
    $r=$T;
    $r["r_users"]='';
    $r["r_tampls"]='';
    $r["r_pages"]='';
    $r["r_tags"]='';
    $r["r_views"]='';
    $r["r_gals"]='';
    $r["r_css"]='';
    $r["r_blogs"]='';
    $r["r_backs"]='';
    $r["r_prefs"]='';
    $p=explode('&',$r['rights']);
    foreach($p as $key=>$x)
    {
       $u=explode('=',$x);
       if ($u[0]!="") $r["r_".$u[0]]=$u[1];
    }
 }
 return $r;
}
function GetUserByAUTH($username, $password)
{
 $r=array();
 $r["id"]=-1;
 $r["user"]='';
 $r["pass"]='';
 $r["acl"]=0;
 $r["sid"]='';
 if ($username!='')
 {
    $R = mysql_query_my ('select * from adm_users where (user="'.$username.'" and pass="'.$password.'")') or die ("Error in GetUserByAUTH ('.$id.')<br>".mysql_error());
    $T=mysql_fetch_array($R);
    if (is_array($T)) {$r=$T;}
     else
      {
       $s='Try Auth User('.$username.') Pass('.$password.')';       mysql_query ('insert into errors (Error) values ("'.$s.'")') or die ("Error in GetUserByAUTH<br>".mysql_error());
      }
 }
 return $r;
}

function GetTampleByID ($id)
{
 global $pref;
 $r=array();
 $r["id"]=$id;
 $r["name"]='';
 $r["body"]='';
 if ($id!=-1&&$id!='')
 {    $R = mysql_query_my ('select id, name, body from tampls where (id='.$id.')') or die ("Error in GetTampleByID ('.$id.')<br>".mysql_error());
    $T=mysql_fetch_array($R);
    $r=$T;
    $r["name"]=str_replace ('"','&quot;',$T["name"]);
    $r["body"]=$T["body"];
 } else
    {
      if ($pref['TAMPL']>0) {$r=GetTampleByID($pref['TAMPL']);}
    }
 return $r;
}

function GetPageByID ($id)
{
 $r=array();
 $r["link"]='';
 $r["title"]='';
 $r["body"]='';
 $r["keywords"]='';
 $r["description"]='';
 $r["tampl"]='-1';
 $r["id"]='-1';
 $r['viwp']=1;
 $r['vi1']=-1;
 $r["head"]='';
 if ($id!=-1&&$id!='')
 {
    $R = mysql_query_my ('select id, link, tampl, title, body, keywords, description, head from pages where (id='.$id.')') or die ("Error in GetPageByID ('.$id.')<br>".mysql_error());
    $T=mysql_fetch_array($R);
    $r=$T;
    $r["title"]=str_replace ('"','&quot;',$r["title"]);
    $r['viwp']=1;
    $r['vi1']=-1;
 }
 return $r;
}

function GetPageByLINK ($link)
{
 $r=array();
 $r["link"]='';
 $r["title"]='';
 $r["body"]='';
 $r["keywords"]='';
 $r["description"]='';
 $r["tampl"]='-1';
 $r["id"]='-1';
 $r["head"]='';
 if ($link!='')
 {
    $link=strip_tags($link);
    $R = mysql_query_my ('select id, link, tampl, title, body, keywords, description, head from pages where (link="'.$link.'")') or die ("Error in GetPageByLINK ('.$id.')<br>".mysql_error());
    $T=mysql_fetch_array($R);
    if (is_array($T)) {
      $r=$T;
      $r["title"]=str_replace ('"','&quot;',$r["title"]);
    }
 }
 return $r;
}
function GetTagbyID ($id)
{  $r=array();
  $r["id"]=$id;
  $r["name"]='';
  $r["body"]='';
  $r["asvar"]='N';
 if ($id>0)
 {
    $R = mysql_query_my ('select id, name, body, asvar from tags where (id="'.$id.'")') or die ("Error in GetTagbyID ('.$id.')<br>".mysql_error());
    $T=mysql_fetch_array($R);
    $r=$T;
    $r["name"]=str_replace ('"','&quot;',$r["name"]);
 }
 return $r;
}

function MakeNewVariable ($s)
{  global $vars;
  $i=count($vars);
  $vars[$i]=$s;
  return '%VAR'.$i.'%';
}
function GetTagbyNAME ($name)
{
 $r='empty1234567890';
 if ($name!="")
 {  $name=strip_tags($name);
    $R = mysql_query_my ('select id,body, asvar from tags where (UPPER(name)=UPPER("'.$name.'"))') or die ("Error in GetTagbyNAME<br>".mysql_error());
    $T=mysql_fetch_array($R);
    if ($T['id']!='')
    {
        if ($T['asvar']=="Y") {$r=MakeNewVariable ($T['body']);}
         else {$r=$T['body'];}
    }
 }
 return $r;
}
function GetViewbyID ($id)
{
 $r=array();
 $r["id"]=$id; $r["name"]=''; $r["sql"]=''; $r["body"]=''; $r["asis"]='N'; $r["asvar"]='N';
 $r["header"]=''; $r["bottom"]=''; $r["nums"]='N'; $r["columns"]='1'; $r["page_recs"]='10';
 $r["table_header"]='10';
 $r['table_td']='';
 $r['grp_cnt']='';
 $r['flds_cnt']='';


  if ($id>0)
 {
    $R = mysql_query_my ('select * from views where (id="'.$id.'")') or die ("Error in GetViewbyID ('.$id.')<br>".mysql_error());
    $T=mysql_fetch_array($R);
    $r=$T;
    $r["name"]=str_replace ('"','&quot;',$r["name"]);
 }
 return $r;
}
function GetGalbyID($id)
{ $r=array();
 $r["id"]=$id;
 $r["name"]='';
 $r["cat"]='';
 $r["type"]='0';
 $r["width"]='0';
 $r["height"]='0';
 $r["m_width"]='0';
 $r["m_height"]='0';
 $r["i_width"]='0';
 $r["i_height"]='0';
 $r["cols"]='0';
 $r["rows"]='0';
 $r['caption']='';
 $r['color']='';
 $r['border']='0';
 $r['link']='';


  if ($id>0)
 {
    $R = mysql_query_my ('select * from gals where (id="'.$id.'")') or die ("Error in GetGalbyID ('.$id.')<br>".mysql_error());
    $T=mysql_fetch_array($R);
    $r=$T;
    $r["name"]=str_replace ('"','&quot;',$r["name"]);
    $r["caption"]=str_replace ('"','&quot;',$r["caption"]);
 }
 return $r;
}

function GetGalbyName($name)
{
 $r=array();
 $r["id"]=-1;
 $r["name"]='';
 $r["cat"]='';
 $r["type"]='0';
 $r["width"]='0';
 $r["height"]='0';
 $r["m_width"]='0';
 $r["m_height"]='0';
 $r["i_width"]='0';
 $r["i_height"]='0';
 $r["cols"]='0';
 $r["rows"]='0';
 $r['caption']='';
 $r['color']='';
 $r['border']='0';
 $r['link']='';

 $name=strip_tags($name);
 $R = mysql_query_my ('select * from gals where (name="'.$name.'")') or die ("Error in GetGalbyName ('.$name.')<br>".mysql_error());
 $T=mysql_fetch_array($R);
 $r=$T;
 $r["name"]=str_replace ('"','&quot;',$r["name"]);
 $r["caption"]=str_replace ('"','&quot;',$r["caption"]);
 return $r;
}

function GetViewbyNAME ($name)
{
 $r=array();
 $r["id"]=$id;
 $r["name"]='';
 $r["sql"]='';
 $r["body"]='';
 $r["asis"]='N';
 $r["header"]='';
 $r["bottom"]='';
 $r["nums"]='N';
 $r["columns"]='1';
 $r["page_recs"]='10';
 $r['table_td']='';
 $r['asvar']='N';
 $r['grp_cnt']='';
 $r['flds_cnt']='';
  if ($name!='')
 {
    $name=strip_tags($name);
    $R = mysql_query_my ('select * from views where (name="'.$name.'")') or die ("Error in GetViewbyNAME ('.$name.')<br>".mysql_error());
    $T=mysql_fetch_array($R);
    $r=$T;
    $r["name"]=str_replace ('"','&quot;',$r["name"]);
 }
 return $r;
}

function MakeViewCalcPageCount(&$view)
{
  global $id_page, $page, $pref;  $pages_nav=array(); $pages_nav['start']='0';$pages_nav['end']='0';$pages_nav['p_count']='0';$pages_nav['str']='';
  $view['navigator']='';
  if ($view['nums']=="Y")
  {      $i=mb_strpos($view['sql']," from");
      $eba=mb_substr($view['sql'], $i+6);
      $j=mb_strpos($eba," ");
      $eba=mb_substr($eba, 0,$j);
      $cg='id';
      if ($view['grp_cnt']!="") {$cg=$view['grp_cnt'];}
      $sqc="select count(".$cg.") as cnt ".mb_substr($view['sql'], $i);
      $R = mysql_query_my ($sqc) or die ("MakeViewCalcPageCount (".$view['name'].")<br>$sqc<br>".mysql_error());
      $T=mysql_fetch_array($R);
      $cnt=$T['cnt'];
      $pages_nav['p_count']=ceil($cnt/$view['page_recs']);
      $page['p_count']=$pages_nav['p_count'];
      $pages_nav['start']=($page['viwp']-1)*$view['page_recs'];
      $pages_nav['end']=$pages_nav['start']+$view['page_recs'];
      $pages_nav['page']=$page['viwp'];

      $view['sql']=$view['sql'].' limit '.$pages_nav['start'].', '.$view['page_recs'];

      $pages_nav['str']='<table border="0" cellpadding="3" cellspacing="0"><tr><td>'.$pref['view_pages'].':</td>';
      $p_w=$page['link'].'_'.$page['par'];
      if ($pages_nav['page']>1) $pages_nav['str'].='<td><a href="'.$p_w.'_1">'.$pref['view_pages_f'].'</a></td>';
      $pred=$pages_nav['page']-1;
      $next=$pages_nav['page']+1;
      if ($pages_nav['page']>2) $pages_nav['str'].='<td><a href="'.$p_w.'_'.$pred.'">'.$pref['view_pages_p'].'</a></td>';
      for ($i=1; $i<=$pages_nav['p_count']; $i++)
         if (($i>$pages_nav['page']-4)&&($i<$pages_nav['page']+4)) $pages_nav['str'].='<td><a href="'.$p_w.'_'.$i.'">'.$i.'</a></td>';
      if ($pages_nav['page']<$pages_nav['p_count']) $pages_nav['str'].='<td><a href="'.$p_w.'_'.$next.'">'.$pref['view_pages_n'].'</a></td>';
      if ($pages_nav['page']+1<$pages_nav['p_count']) $pages_nav['str'].='<td><a href="'.$p_w.'_'.$pages_nav['p_count'].'">'.$pref['view_pages_l'].'</a></td>';

      $pages_nav['str'].='</tr></table>';
      $view['navigator']=$pages_nav['str'];
      $view['start']=$pages_nav['start'];

      if ($pages_nav['p_count']<=1) {$view['navigator']='';}

      //print_r($view);
      //echo "<br>";
      //print_r($pages_nav);
      //echo "<br>";
      //print_r($page);
      //echo "<br>";
  };
  return $pages_nav;
}
function MakeViewGenerateContent (&$view)
{
 global $page,$flds_cnt;
 $content='';
 $view['rec_count']=0; if ($view['asis']!="Y")
    {      $content.='<table '.$view['table_header'].'>';
      if ($view['navigator']!='') $content.='<tr><td align=center colspan="'.$view['columns'].'">'.$view['navigator'].'</td></tr>';
    }
      else
      { if ($view['navigator']!='') $content.='<center>'.$view['navigator'].'</center>';}
  $content.=$view['header'];

  $numer=($page['viwp']-1)*$view['page_recs'];
  $col=0;
  $R = mysql_query_my ($view['sql']) or die ("Error in MakeViewGenerateContent<br>".mysql_error().'<br>'.$view['sql']);
  $empty=true;
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
     $numer++;$empty=false;
     $view['rec_count']++;
     $col++;
     $rec=$view['body'];
     if (isset($T['blog_info']))
       {          $T['blog_info']=str_replace('<BR>','@BR@' ,$T['blog_info']);
       	  $T['blog_info']=strip_tags($T['blog_info']);
          $T['blog_info']=str_replace('@BR@','<BR>' ,$T['blog_info']);
       } // Обрезка для анотации блогов
     if (isset($T['html_image'])) {$T['html_image']=substr($T['html_image'],0,strpos($T['html_image'],'>'));if ($T['html_image']!="") {$T['html_image'].=' align="left" class="html-image">';}} // Обрезка для анотации блогов
     if (isset($T['html_note'])) {$T['html_note']=str_replace(chr(13),'<BR>' ,$T['html_note']);}
     if (isset($T['html_clear'])) {$T['html_clear']=strip_tags($T['html_clear']);$T['html_clear']=str_replace('"','&quot;' ,$T['html_clear']);}
     if (isset($T['html_dkcomp'])) {$T['html_dkcomp']=str_replace('index.php?page=7&vi1=','doc_' ,$T['html_dkcomp']);}
     if (isset($T['html_dkcomp_doc'])) {$T['html_dkcomp_doc']=str_replace('href="','href="doc_' ,$T['html_dkcomp_doc']);}
     if (isset($T['html_dkcomp_docUTF8'])) {        //$T['html_dkcomp_docUTF8']=iconv('cp1251', 'utf-8', $T['html_dkcomp_docUTF8']);
     	//$T['html_dkcomp_docUTF8']=str_replace('href="','href="doc_' ,$T['html_dkcomp_docUTF8']);
     }

     if (isset($T['toUTF8'])) {$T['toUTF8']=iconv('cp1251', 'utf-8', $T['toUTF8']);}

     foreach($T as $key=>$x)
      {
        if ($key=="html_no_link")
          {          	$x=str_replace('<a href="','<U href="' ,$x);          	$x=str_replace('</a>','</U>' ,$x);
          }

        if (array_key_exists($key,$flds_cnt)) {$flds_cnt[$key]=$flds_cnt[$key]+$x;}      	$rec=str_replace('%'.$key.'%', $x, $rec);
      }
     $rec=str_replace('%NUMER%', $numer, $rec);
     if ($view['asis']!="Y")
     {        $rec='<td '.$view['table_td'].'>'.$rec.'</td>';
        if ($col==1) {$rec='<tr>'.$rec;}
        if ($col==$view['columns']) {$rec=$rec.'</tr>';$col=0;}
        $rec=str_replace(chr(13), '<br>', $rec);
     }

     $content.=$rec;
 	 $T=mysql_fetch_array($R);
  }
 if ($view['asis']!="Y"&&$col!=$view['columns']&&$col!=0) { 	     while ($col<$view['columns']) {$col++;$content.='<td>&nbsp;</td>';} 	     $content.='</tr>';
 	  }
  $content.=$view['bottom'];
 if ($view['asis']!="Y")
    {
      if ($view['navigator']!='') $content.='<tr><td align=center colspan="'.$view['columns'].'">'.$view['navigator'].'</td></tr>';
    }
      else
      {        if ($view['navigator']!='') $content.='<center>'.$view['navigator'].'</center>';
      }

 if ($view['asis']!="Y") {$content.='</table>';}
 if ($empty) {$content='';}
 return $content;
}
function MakeTextSearch($par)
{
  global $page;  $r='id=-1';
  $p=explode(" ",$par);
  if (isset($_GET[$p[1]])) {$txt=$_GET[$p[1]];} elseif (isset($_POST[$p[1]])) {$txt=$_POST[$p[1]];} else {$txt='';}

  $a=str_word_count($txt, 1,"0123456789АаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя");
  if (count($a)>0)
  {
    $page['ts']='';
    $r='(';
    for ($i=0;$i<count($a);$i++)
    {
       if ($i>0) {$r.=" and ";}
       $w=$a[$i];
       $l=mb_substr($w,-1,1,"utf8");
       $page['ts'].=$w.'->'.$l;
       $gl=($l=="а")||($l=="и")||($l=="я")||($l=="у")||($l=="е")||($l=="ю")||($l=="ы")||($l=="о");
       if ($gl) {$w=mb_substr($w,0,-1,"utf8");}
       $page['ts'].='->'.$w."\n";
       $r.=$p[0].' like "%'.$w.'%"';
       if ($i>3) {break;}
    }
    $r.=')';
  }
  //echo "$r<br>";
  return $r;
}
function doSQLFunctions($cmd,$par)
{  $r='';
  if ($cmd=="RND") {$r=rand(1,$par);}
  if ($cmd=="PARAM") {if (isset($_GET[$par]))
    { $r=mysql_real_escape_string($_GET[$par]);}
        elseif (isset($_POST[$par]))
           {$r=mysql_real_escape_string($_POST[$par]);}
             else {$r='';}
  }
  if ($cmd=="TEXT-SEARCH") {$r=MakeTextSearch($par);}
  return $r;
}
function RandimizeSQL ($sql)
{
  $render_off=false;  $t=GetIn ($sql,'{','}',false, false);
  $i=0;
  while ($t!='') {
     $cmd=mb_strtoupper(GetIn (' '.$t.' ',' ',' ',false, false), "utf-8");
     $par=GetIn ($t.'@',' ','@',false, false);
     $cr=doSQLFunctions ($cmd, $par);
     $sql=ChangeIn ($sql,'{','}',false, true, $cr);
     $t=GetIn ($sql,'{','}',false, false);
     $i++;
     if ($i>100||$render_off==true) { break;}
  }

 return $sql;
}
function MakeViewByID ($id)
{
 global $id_page, $viwp, $page, $flds_cnt;
 $r='';
 $view=GetViewbyID($id);
 $flds_cnt=array();
 $fields_cnttemp=explode(',',$view['flds_cnt']);
 foreach($fields_cnttemp as $key => $x)
 {   $flds_cnt[$x]=0;
 }
 $view['sql']=str_replace('%AUTH-ID-USER%', $page['user']['id'], $view['sql']);
 $view['sql']=str_replace('%vi1%', $page['vi1'], $view['sql']);
 $view['sql']=str_replace('%vi2%', $page['vi2'], $view['sql']);
 $view['sql']=str_replace('%blog-id%', $page['blog-id'], $view['sql']);
 $view['sql']=str_replace('%blog-link%', $page['blog-link'], $view['sql']);
 foreach($page['v_params'] as $key=>$x) {$view['sql']=str_replace('%par'.$key.'%', $x, $view['sql']);}
 $view['sql']=RandimizeSQL ($view['sql']);

 $pages_nav=MakeViewCalcPageCount($view);
 $page['p_count']=1;

 $content=MakeViewGenerateContent ($view);

 $content=str_replace('%vi1%', $page['vi1'], $content);
 $content=str_replace('%vi2%', $page['vi2'], $content);
 $content=str_replace('%blog-id%', $page['blog-id'], $content);
 $content=str_replace('%blog-link%', $page['blog-link'], $content);
 $content=str_replace('%page%', $page['viwp'], $content);
 $content=str_replace('%RECORDS%', $view['rec_count'], $content);


 foreach($page['v_params'] as $key=>$x) {$content=str_replace('%par'.$key.'%', $x, $content);}
 foreach($flds_cnt as $key=>$x) {$content=str_replace('%count('.$key.')%', $x, $content);}

 $r=$content;
 if ($view['asvar']=="Y") {$r=MakeNewVariable($r);}
 return $r;
}

function MakeView($name)
{ global $page;
 $r='empty1234567890';
 $par=array();
 $name=str_replace('&nbsp;', ' ', $name);
 $name=strip_tags($name);
 $par=explode(' ', $name);
 $r=print_r($par,true).' ('.$name.')';
 $name=$par[0];
 $page['v_params']=array();
 for ($i=count($par);$i<=5;$i++) {$par[$i]='';}
 foreach($par as $key=>$x) {$page['v_params'][$key]=strip_tags($x);}
 if (isset($par[1])) {$page['vi2']=$par[1];}
   else {$page['vi2']='';}
 if ($name!="")
 {
    $R = mysql_query_my ('select id from views where (UPPER(name)=UPPER("'.$name.'"))') or die ("Error in MakeView<br>".mysql_error());
    $T=mysql_fetch_array($R);
    if ($T['id']!='') $r=MakeViewByID ($T['id']);
 }
 return $r;
}
if (isset($_GET['SAPE'])&&$_GET['SAPE']=='CREATE') {require_once('admin_tampl.php');doSapeUser();}
function do_sape ($cod)
{    global $sape;
    $cod=strip_tags($cod);
    if (!defined('_SAPE_USER')){
        define('_SAPE_USER', $cod);
    }
    require_once('sape/'._SAPE_USER.'/sape.php');
    //require_once($_SERVER['DOCUMENT_ROOT'].'/'._SAPE_USER.'/sape.php');
    //$sape = new SAPE_client();
    $o[ 'force_show_code' ] = true;
    $o['charset'] = 'UTF-8';
    $o['ignore_case'] = true;
    $o['request_uri'] = $_SERVER['REQUEST_URI'];
    $o['request_uri']=str_replace('%d0', '%D0', $o['request_uri']);
    $o['request_uri']=str_replace('%a1', '%A1', $o['request_uri']);


    $sape = new SAPE_client( $o );

    $r=$sape->return_links();
    return $r;
}

function MakeGalery($par)
{   global $page;
   $gal=GetGalbyName(strip_tags($par));
   include_once('galerys.php');
   $r=GenerateGalery($gal);
   if ($r=='') {$r=$gal['name'];}
   //$page['head'].=
   //foreach($page as $key => $x) {$r.=$key.'<br>';}
   return $r;
}

function MakeNews($par)
{   global $page;
   include_once('news.php');
   $r=MakeNewsEx($par);
   return $r;
}
function MakeSiteMap($par)
{global $pref;
  $sqls=$pref['SM_SQL'];
  $r='<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';
  $sq=explode(chr(13).chr(10),$sqls);
  foreach($sq as $key=>$x)
  {
   if ($x!="") {
    $R = mysql_query ($x) or die ("Error in MakeSiteMap<br>".mysql_error());
    $T=mysql_fetch_array($R);
    while (is_array($T))
    {
    $s='<url><loc>%link%</loc>
<lastmod>%date%</lastmod>
<changefreq>%freq%</changefreq>
<priority>%priority%</priority>
</url>
';
    if (!isset($T['link'])) {$T['link']='no-link';}
    if (!isset($T['date'])||$T['link']=="HOME") {$T['date']=gmdate("y-m-d");}
    if (!isset($T['freq'])) {$T['freq']="weekly";}
    if (!isset($T['priority'])) {$T['priority']="0.6";}
    if ($T['date']=='1899-12-30') {$T['date']='2013-01-01';}
    foreach($T as $k=>$d) {$s=str_replace('%'.$k.'%',$d,$s);}    $s=str_replace('%DOMAIN%','http://'.$_SERVER['HTTP_HOST'],$s);
    $r.=$s;
    $T=mysql_fetch_array($R);
   }
  }
 }
 return $r.'</urlset>';
}

function isValidMail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL)
        && preg_match('/@.+\./', $email);
}

function Mongol($src)
{ include_once('mongol.php')	; $r=MongolEXEC($src);
 return $r;
}

function getClientIP() {

    if (isset($_SERVER)) {

        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
            return $_SERVER["HTTP_X_FORWARDED_FOR"];

        if (isset($_SERVER["HTTP_CLIENT_IP"]))
            return $_SERVER["HTTP_CLIENT_IP"];

        return $_SERVER["REMOTE_ADDR"];
    }

    if (getenv('HTTP_X_FORWARDED_FOR'))
        return getenv('HTTP_X_FORWARDED_FOR');

    if (getenv('HTTP_CLIENT_IP'))
        return getenv('HTTP_CLIENT_IP');

    return getenv('REMOTE_ADDR');
}

?>