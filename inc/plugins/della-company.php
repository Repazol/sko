<?php

function GetMyCompanyTab()
{
  global $page,$contact;
  $r='';
  if (isset($_POST["docomment"])&&$_POST["docomment"]=="MyCompany")
  {
    $id_c=$page['vars']['id_company'];
    //$r='<pre>'.print_r($_POST,true).'</pre>';
    //$r.='Сохранено'.$id_c.'<hr>';
    $cn=mysql_real_escape_string(GetPostGetParam('company'));
    $addr=mysql_real_escape_string(GetPostGetParam('addr'));
    $cty=mysql_real_escape_string(GetPostGetParam('city'));
    $tel1=mysql_real_escape_string(GetPostGetParam('tel1'));
    $tel2=mysql_real_escape_string(GetPostGetParam('tel2'));
    $tel3=mysql_real_escape_string(GetPostGetParam('tel3'));
    $skype=mysql_real_escape_string(GetPostGetParam('skype'));
    $ad='tel1="'.$tel1.'", tel2="'.$tel2.'", tel3="'.$tel3.'", skype="'.$skype.'"';
    $id_city=GetPostGetParam('id_city')+0;
    if ($id_city<1)
    {
      $r.='<div class="notice error"><i class="icon-remove-sign icon-large"></i> Местоположение не найденно <b>'.$cty.'</b> <a href="#close" class="icon-remove"></a></div>';
    }
    else
    {
       $sql='update t_companys set id_city='.$id_city.', name="'.$cn.'", addr="'.$addr.'", '.$ad.' where (id='.$id_c.')';
       $R = mysql_query_my ($sql) or die ("Error in GetMyCompanyTab post<br>".mysql_error().'<br>'.$sql);
       $r.='<div class="notice success"><i class="icon-ok icon-large"></i> Сохранено <a href="#close" class="icon-remove"></a></div>';
    }

  }

  $page['head'].='<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">';
  $page['head'].='<script src="js/jquery-ui-min.js"></script>';
  $page['head'].='<script src="js/jquery.mask.min.js"></script>';
  $page['head'].='<script>
  $(function() {
    $("#tel1").mask("+9(999)-999-9999");
    $("#tel2").mask("+9(999)-999-9999");
    $("#tel3").mask("+9(999)-999-9999");
    $( "#city" ).autocomplete({
      source: "gc.php",
      select: function( event, ui ) {
        $( "#id_city" ).val( ui.item.id );
        $(this).val( ui.item.value );
        return false;
        },

         minLength: 2,

    });
  });
  </script>';

  /*



  */
  include_once('inc/engine.php');
  $sql='select t_userinfo.admin,t_companys.name as company, t_contacts.fio,t_companys.id_city,';
  $sql.='t_companys.addr,t_companys.subj,t_companys.tel1, t_companys.tel2,t_companys.tel3,t_companys.skype, t_companys.mail from t_userinfo ';
  $sql.='left join t_companys on t_companys.id=t_userinfo.id_company ';
  $sql.='left join t_contacts on t_contacts.id=t_userinfo.id_contact ';
  $sql.='where (t_userinfo.id_user='.$page['user']['id'].')';
  $R = mysql_query_my ($sql) or die ("Error in GetMyCompanyTab<br>".mysql_error().'<br>'.$sql);
  $T=mysql_fetch_array($R);
  $city=DellaGetCity($T['id_city']);
  $mail=$T['mail'];
  $subj='Юридическое лицо';
  if ($T['subj']==2) {$subj='Частный предпринематель';}
  if ($T['subj']==3) {$subj='Физическое лицо';}
  if ($city=="") {$city='Местоположение не указанно';}
  $tel='<table>';
  $tel.='<tr><td class="flabel">Телефон 1:</td><td><input id="tel1" class="ffieldinp" name="tel1" type="text" value="'.$T['tel1'].'"></td></tr>';
  $tel.='<tr><td class="flabel">Телефон 2:</td><td><input id="tel2" class="ffieldinp" name="tel2" type="text" value="'.$T['tel2'].'"></td></tr>';
  $tel.='<tr><td class="flabel">Телефон 3:</td><td><input id="tel3" class="ffieldinp" name="tel3" type="text" value="'.$T['tel3'].'"></td></tr>';
  $tel.='<tr><td class="flabel">Skype:</td><td><input class="ffieldinp" name="skype" type="text" value="'.$T['skype'].'"></td></tr>';
  $tel.='</table>';
  if ($page['vars']['admin']!='Y') {
  $r.='<b>'.$T['company'].'</b> ('.$city.')<br>';
  $r.='<b>Адрес:</b><br>'.$T['addr'];
  } else
    {
      $r.='<form name="" action="" method="post"><input name="docomment" type="hidden" value="MyCompany">';
      $r.='<table>';
      $r.='<tr><td style="text-align:right;width:150px;">Компания:</td><td><input class="ffieldinp" name="company" type="text" value="'.$T['company'].'"></td><td><b>'.$subj.'</b></td></tr>';
      $r.='<tr><td style="text-align:right;width:150px;">Город:</td><td><input id="city" class="ffieldinp" name="city" type="text" value="'.$city.'"></td><td><b>E-Mail:</b>'.$mail.'</td></tr>';
      $r.='<tr><td valign="top" style="text-align:right;width:150px;">Адрес:</td><td><textarea class="ffieldinp" name="addr" rows=5 cols=20 wrap="off">'.$T['addr'].'</textarea></td><td valign="top">'.$tel.'</td></tr>';
      $r.='<tr><td></td><td><input id="id_city" name="id_city" type="hidden" value="'.$T['id_city'].'"><input type="submit" value="Сохранить"></td></tr>';
      $r.='</table>';

      $r.='</form>';
    }

  return $r;
}
function GetMyCompanyTab2()
{
  global $page;
  $r='';
  include_once('inc/engine.php');
  $sql='select t_userinfo.id_user,t_contacts.fio,t_contacts.id_city,t_contacts.tel1,t_contacts.tel2,t_contacts.tel3,t_contacts.mail,t_contacts.skype from t_userinfo ';
  $sql.='left join t_contacts on t_contacts.id=t_userinfo.id_contact ';
  $sql.='where (t_userinfo.id_company='.$page['user']['id'].')';
  $R = mysql_query_my ($sql) or die ("Error in GetMyCompanyTab<br>".mysql_error().'<br>'.$sql);
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
    $r.='<table>';
    $r.='<tr><td colspan=2><b>'.$T['fio'].' ('.DellaGetCity($T['id_city']).')</td></tr>';
    $r.='<tr><td rowspan=2>Тел.:'.$T['tel1'].'<br>Тел.2:'.$T['tel2'].'<br>Тел.3:'.$T['tel3'].'</td>';
    $r.='<td>E-Mail:'.$T['mail'].'</td></tr><tr><td>Skype:'.$T['skype'].'</td></tr>';
    $r.='</table>';
    $T=mysql_fetch_array($R);
  }
  return $r;
}

function GetCargoSityList ($pref, $id_cargo)
{  $r='';
  $R = mysql_query_my ('select id_city from t_cargos_'.$pref.' where (id_cargo='.$id_cargo.')') or die ("Error in GetCargoSityList<br>".mysql_error().'<br>'.$sql);
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {  	$r.=DellaGetCity($T['id_city']).';<br>';
    $T=mysql_fetch_array($R);
  }
  return $r;
}
function GetTransportSityList ($pref, $id_cargo)
{
  $r='';
  $R = mysql_query_my ('select id_city from t_transports_'.$pref.' where (id_cargo='.$id_cargo.')') or die ("Error in GetTransportSityList<br>".mysql_error().'<br>'.$sql);
  $T=mysql_fetch_array($R);
  while (is_array($T))
  {
  	$r.=DellaGetCity($T['id_city']).';<br>';
    $T=mysql_fetch_array($R);
  }
  return $r;
}

function GetMyCompanyCargosTab()
{
  global $page;  $r='';
  $sql='select id,dt,dt1,dt2,cargo,note from t_cargos where (id_company='.$page['vars']['id_company'].')';
  $R = mysql_query_my ($sql) or die ("Error in GetMyCompanyTab<br>".mysql_error().'<br>'.$sql);
  $T=mysql_fetch_array($R);
  $r.='<table class="sortable cargo">';
  $r.='<thead><tr>';
  $r.='<th>№</th><th>Груз</th><th>Даты</th><th>Загрузка</th><th>Выгрузка</th><th>Коментарий</th>';

  $r.='</tr></thead>';
  $n=0;
  while (is_array($T))
  {
    $n++;
    $from=GetCargoSityList ('out',$T['id']);
    $to=GetCargoSityList ('in',$T['id']);
    $a='<a href="AddEditCargo_'.$T['id'].'">';  	$r.='<tr>';
  	$r.='<td>'.$a.$n.'</a></td><td>'.$a.$T['cargo'].'</a></td><td>'.$a.$T['dt1'].'-'.$T['dt2'].'</a></td><td>'.$from.'</td><td>'.$to.'</td><td>'.$T['note'].'</td>';
  	$r.='<td><a href="cargo-map_'.$T['id'].'">Инфо</a></td>';
  	$r.='<td><a href="DelCargo_'.$T['id'].'">X</a></td>';

  	$r.='</tr>';
    $T=mysql_fetch_array($R);
  }
  $r.='</table>';
  return $r;
}
function GetMyCompanyTransportTab()
{  global $page;
  $r='';
  $sql='select id,dt,dt1,dt2,note from t_transports where (id_company='.$page['vars']['id_company'].')';
  $R = mysql_query_my ($sql) or die ("Error in GetMyCompanyTab<br>".mysql_error().'<br>'.$sql);
  $T=mysql_fetch_array($R);
  $r.='<table class="sortable cargo">';
  $r.='<thead><tr>';
  $r.='<th>№</th><th>Даты</th><th>Загрузка</th><th>Выгрузка</th><th>Коментарий</th>';

  $r.='</tr></thead>';
  $n=0;
  while (is_array($T))
  {
    $n++;
    $from=GetTransportSityList ('out',$T['id']);
    $to=GetTransportSityList ('in',$T['id']);
    $a='<a href="AddTransport_'.$T['id'].'">';
  	$r.='<tr>';
  	$r.='<td>'.$a.$n.'</a></td><td>'.$a.$T['dt1'].'-'.$T['dt2'].'</a></td><td>'.$from.'</td><td>'.$to.'</td><td>'.$T['note'].'</td>';
  	$r.='<td><a href="transport-map_'.$T['id'].'">Инфо</a></td>';
  	$r.='<td><a href="DelTransport_'.$T['id'].'">X</a></td>';

  	$r.='</tr>';
    $T=mysql_fetch_array($R);
  }
  $r.='</table>';
  return $r;

}
function MyCompany($par)
{
  $r='<ul class="tabs left" style="margin-top:0px;">';
  $r.='<li><a href="#MyCompany">Моя компания</a></li>';
  $r.='<li><a href="#Personals">Мои сотрудники</a></li>';
  $r.='<li><a href="#Cargos">Мои грузы</a></li>';
  $r.='<li><a href="#Transports">Мой транспорт</a></li>';
  $r.='</ul>';

  $r.='<div id="MyCompany" class="tab-content">'.GetMyCompanyTab().'</div>';
  $r.='<div id="Personals" class="tab-content">'.GetMyCompanyTab2().'</div>';
  $r.='<div id="Cargos" class="tab-content">'.GetMyCompanyCargosTab().'</div>';
  $r.='<div id="Transports" class="tab-content">'.GetMyCompanyTransportTab().'</div>';
  return $r;
}


?>