<?php

function DellaGetCityIDByName($city)
{  $r=-1;
  $sql='select city_id where (name="'.$city.'")';
  $R = mysql_query_my ($sql) or die ("Error in DellaGetCityIDByName<br>".mysql_error().'<br>'.$sql);
  $T=mysql_fetch_array($R);
  if (is_array($T))
  {    $r=$T['city_id'];
  }
 return $r;
}
                                  //KUZNETSOV150514 150514 7772102877
function DellaGetCity($id, $map=false)
{  $r='';
  if ($id+0==0) {$id=-1;}
  $sql='select t_city.name as c,t_country.name as s,t_region.name as r from t_city ';
  $sql.='left join t_country on t_country.country_id=t_city.country_id ';
  $sql.='left join t_region on t_region.region_id=t_city.region_id ';
  $sql.='where t_city.city_id='.$id;
  $R = mysql_query_my ($sql) or die ("Error in DellaGetCity<br>".mysql_error().'<br>'.$sql);
  $T=mysql_fetch_array($R);
  if (is_array($T))
    {      if ($map) {$r=$T['c'].','.$T['s'];}
        else {$r=$T['c'].' '.$T['s'].' '.$T['r'];}
    }
  return $r;
}
function DellaInitUser($par)
{  global $page, $contact;
  $r='';
  $page['vars']['company']='Компания не указана';
  $page['vars']['fio']='Имя не указано';
  $page['vars']['admin']='N';
  $page['vars']['id_contact']=-1;
  $page['vars']['id_company']=-1;
  if ($page['user']['id']!=-1) {
  // User info init
  $sql='select id_user, id_contact,id_company,admin from t_userinfo where (id_user='.$page['user']['id'].')';
  $R = mysql_query_my ($sql) or die ("Error in DellaInitUser 1<br>".mysql_error().'<br>'.$sql);
  $T=mysql_fetch_array($R);
  if (!is_array($T))
   {     $sql='insert into t_userinfo (id_user) values ('.$page['user']['id'].')';
     $R = mysql_query_my ($sql) or die ("Error in DellaInitUser 1<br>".mysql_error().'<br>'.$sql);

   }
     else {$page['vars']['id_contact']=$T['id_contact'];$page['vars']['id_company']=$T['id_company'];$page['vars']['admin']=$T['admin'];}

  //User init contact     !!!!!!!!!!!! $contact !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
  $sql='select * from t_contacts where (id='.$page['vars']['id_contact'].')';
  $R = mysql_query_my ($sql) or die ("Error in DellaInitUser 2<br>".mysql_error().'<br>'.$sql);
  $T=mysql_fetch_array($R);
  if (is_array($T))
    {      $contact=$T;
      $page['vars']['fio']=$T['fio'];
    } else {      $page['vars']['fio']='<a href="Reg2">Продолжить регистрацию</a>';
      if ($page['link']!="Reg2")
      {
         header("Location: Reg2");
         exit;
      }
    }
  //User init company
  $sql='select name from t_companys where (id='.$page['vars']['id_company'].')';
  $R = mysql_query_my ($sql) or die ("Error in DellaInitUser 3<br>".mysql_error().'<br>'.$sql);
  $T=mysql_fetch_array($R);
  if (is_array($T))
  {    $page['vars']['company']=$T['name'];

  } else
   {      if ($page['link']!="Reg3"&&$page['link']!="Reg2") {      	header("Location: Reg3");
        exit;
      }
   }

  }
  return $r;
}
function DellaTopMenu($par)
{  $r='<a class="della-top-menu" href="AddTransport">Добавить транспорт</a> | <a class="della-top-menu" href="AddEditCargo">Добавить груз</a> ';
  $r.='| <a class="della-top-menu" href="CargoSearch">Поиск грузов и транспорта</a> ';
  return $r;
}
function GetCityDataEx ($class,$index, &$id_city, &$city)
{
  global $page;
  $c='from';
  if ($class=="totowns") {$c='to';}  if (isset($page['cargo'][$c][$index]))
  {
     $id_city=$page['cargo'][$c][$index];
     $city=DellaGetCity($id_city);
  } else {$id_city=-1;$city='';}

}

function PluginRender($cmd,$par,$cr)
{
 global $page;
 if ($page['user']['id']==-1) {$cr='';
    if ($cmd!="DELLA-INIT") {$cr='<div class="notice warning"><i class="icon-warning-sign icon-large"></i> Необходимо войти в систему <a href="#close" class="icon-remove"></a></div>';}

   }
   else
    {
      if ($cmd=="DELLA-INIT") {$cr=DellaInitUser($par);}

      if ($cmd=="DELLA-MYCOMPANY") {include_once('della-company.php');$cr=MyCompany($par);}
      if ($cmd=="DELLA-REG2") {include_once('della-reg.php');$cr=DellaReg2($par);}
      if ($cmd=="DELLA-REG3") {include_once('della-reg.php');$cr=DellaReg3($par);}

      if ($cmd=="DELLA-PROFILE") {include_once('della-reg.php');$cr=DellaProfile($par);}
      if ($cmd=="DELLA-ADDTRANSPORT") {$cr='DELLA-ADDTRANSPORT';}
      if ($cmd=="DELLA-ADDCARCO") {include_once('della-trcargo.php');$cr=AddEditCargo($par);}
      if ($cmd=="DELLA-ADDTRANSPORT") {include_once('della-trcargo.php');$cr=AddEditTransport($par);}




    }
 if ($cmd=="DELLA-TOPMENU") {$cr=DellaTopMenu($par);}
 if ($cmd=="DELLA-CARGOMAP") {include_once('della-trcargo.php');$cr=CargoMap($par);}
 if ($cmd=="DELLA-TRANSPMAP") {include_once('della-trcargo.php');$cr=TransportMap($par);}
 if ($cmd=="DELLA-CITYS") {include_once('della-trcargo.php');$cr=SearchCitysHtml($par);}
 if ($cmd=="DELLA-CARGOSEARCH") {include_once('della-search.php');$cr=CargoSearch($par);}


 return $cr;//.'<pre>'.print_r($page['user'],true).'</pre>';
}

?>