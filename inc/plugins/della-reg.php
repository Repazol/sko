<?php

function DellaReg2($par)
{
  global $page;

  $r='';
  $valid=false;
  $err='';
  $fio='';
  $id_city='-1';
  $tel1='+7(000)-000-0000';
  $tel2='+7(000)-000-0000';
  $tel3='+7(000)-000-0000';
  $skype='';
  $city='';

  if (isset($_POST['docomment'])&&$_POST['docomment']=="Reg2")
  {
    $fio=mysql_real_escape_string(GetPostGetParam('fio'));
    $id_city=mysql_real_escape_string(GetPostGetParam('id_city'));
    $city=DellaGetCity($id_city);
    $tel1=mysql_real_escape_string(GetPostGetParam('tel1'));
    $tel2=mysql_real_escape_string(GetPostGetParam('tel2'));
    $tel3=mysql_real_escape_string(GetPostGetParam('tel3'));
    $skype=mysql_real_escape_string(GetPostGetParam('skype'));
    if ($fio=="") {$err.='Пожалуйста укажите Фамилию Имя Отчество</br>';}
    if ($tel1==""&&$tel2==""&&$tel3=="") {$err.='Пожалуйста укажите хоть один телефон</br>';}
    if ($id_city==-1) {$err.='Пожалуйста укажите город</br>';}
    if ($err=="")
    {
      $valid=true;
      $mail=$page['user']['mail'];
      $sql='insert into t_contacts (fio,id_city,tel1,tel2,tel3,mail,skype) values ("'.$fio.'",'.$id_city.',"'.$tel1.'","'.$tel2.'","'.$tel3.'","'.$mail.'","'.$skype.'")';
      $R = mysql_query_my ($sql) or die ("Error in Reg 2<br>".mysql_error().'<br>'.$sql);
      $sql='select LAST_INSERT_ID() as id from t_contacts';
      $R = mysql_query_my ($sql) or die ("Error in Reg 2 (2)<br>".mysql_error().'<br>'.$sql);
      $T=mysql_fetch_array($R);
      if (is_array($T))
      {
        $id_c=$T['id'];
        $sql='update t_userinfo set id_contact='.$id_c.' where (id_user='.$page['user']['id'].')';
        $R = mysql_query_my ($sql) or die ("Error in Reg 2 (3)<br>".mysql_error().'<br>'.$sql);
        header("Location: Reg3");
        exit;
      } else {$r.='Ошибка сервера';}
    }
  }

  if (!$valid) {
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

  $r.='<form name="" action="" method="post"><input name="docomment" type="hidden" value="Reg2"><table>';
  $r.='<tr><td class="flabel">ФИО:</td><td><input class="ffieldinp" name="fio" type="text" value="'.$fio.'"></td><td valign="top" rowspan="7"><font color="red">'.$err.'</font></td></tr>';
  $r.='<tr><td class="flabel">Город:</td><td><input class="ffieldinp" id="city" name="city" type="text" value="'.$city.'"><input id="id_city" name="id_city" type="hidden" value="'.$id_city.'"></td></tr>';
  $r.='<tr><td class="flabel">E-Mail:</td><td>'.$page['user']['mail'].'</td></tr>';
  $r.='<tr><td class="flabel">Телефон 1:</td><td><input id="tel1" class="ffieldinp" name="tel1" type="text" value="'.$tel1.'"></td></tr>';
  $r.='<tr><td class="flabel">Телефон 2:</td><td><input id="tel2" class="ffieldinp" name="tel2" type="text" value="'.$tel2.'"></td></tr>';
  $r.='<tr><td class="flabel">Телефон 3:</td><td><input id="tel3" class="ffieldinp" name="tel3" type="text" value="'.$tel3.'"></td></tr>';
  $r.='<tr><td class="flabel">Skype:</td><td><input class="ffieldinp" name="skype" type="text" value="'.$skype.'"></td></tr>';
  $r.='<tr><td class="flabel"></td><td><input type="submit" value="Продолжить"></td></tr>';
  $r.='</table></form>';
  }


  return $r;
}

function NewCompany ($name, $id_city, $addr)
{
  global $page;
  $r='';
  $name='';
  $id_city='-1';
  $city='';
  $tel1='+7(000)-000-0000';
  $tel2='+7(000)-000-0000';
  $tel3='+7(000)-000-0000';
  $mail='';
  $skype='';
  $sub='1';
  $addr='';
  $err='';

  $valid=false;
  if (isset($_POST['docomment'])&&$_POST['docomment']=="Reg3")
  {
    $name=mysql_real_escape_string(GetPostGetParam('name'));
    $id_city=mysql_real_escape_string(GetPostGetParam('id_city'));
    $city=DellaGetCity($id_city);
    $tel1=mysql_real_escape_string(GetPostGetParam('tel1'));
    $tel2=mysql_real_escape_string(GetPostGetParam('tel2'));
    $tel3=mysql_real_escape_string(GetPostGetParam('tel3'));
    $mail=mysql_real_escape_string(GetPostGetParam('mail'));
    $skype=mysql_real_escape_string(GetPostGetParam('skype'));
    $sub=mysql_real_escape_string(GetPostGetParam('bedStatus'))+0;
    $addr=mysql_real_escape_string(GetPostGetParam('addr'));

    $s='';
    if ($sub==1) {$s='название';}
    if ($sub==2) {$s='Фамилию и.о. предпринимателя';}
    if ($sub==3) {$s='Фамилию';}
    if ($s=='') {$s='';$err='Ошибка</br>';}

    if (strlen($name)<2) {$err.='Укажите '.$s.'</br>';}
    if (!isValidMail($mail)) {$err.='E-Mail указан не верно</br>';}
    if ($id_city==-1) {$err.='Местоположение указанно не верно</br>';}
    if ($err=='')
    {
      $valid=true;
      $sql='insert into t_companys (name,id_city,addr,subj,tel1,tel2,tel3,mail,skype) values ("'.$name.'",'.$id_city.',"'.$addr.'",'.$sub.',"'.$tel1.'","'.$tel2.'","'.$tel3.'","'.$mail.'","'.$skype.'")';
      $R = mysql_query_my ($sql) or die ("Error in Reg 3<br>".mysql_error().'<br>'.$sql);
      $sql='select LAST_INSERT_ID() as id from t_companys';
      $R = mysql_query_my ($sql) or die ("Error in Reg 3 (2)<br>".mysql_error().'<br>'.$sql);
      $T=mysql_fetch_array($R);
      if (is_array($T))
      {
        $id_c=$T['id'];
        $sql='update t_userinfo set id_company='.$id_c.', admin="Y", allow="Y" where (id_user='.$page['user']['id'].')';
        $R = mysql_query_my ($sql) or die ("Error in Reg 3 (2)<br>".mysql_error().'<br>'.$sql);
        header("Location: RegDone");
        exit;
      } else {$r.='Ошибка сервера';}
    }

  }
  if (!$valid)
  {
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
$(document).ready(function() {
    $("input:radio[name=bedStatus]").change(function() {
    	s="??";
    	k="'.$page['vars']['fio'].'";
        if (this.value == "1") {
            s="Название компании:";
            $("#nm").val("");
        }
        if (this.value == "2") {
            s="Фамилия и.о. предпринимателя:";
            $("#nm").val(k);
        }
        if (this.value == "3") {
            s="Фамилия и.о.:";
            $("#nm").val(k);
        }
        $("#name-label").text(s);
    });
});
</script> ';

  $city=DellaGetCity($id_city);
  $subj='<div style="font-size:9pt;">';
  $a=array(1=>'Юридическое лицо', 2=>'Предприниматель', 3=>'Частное лицо');
  foreach($a as $k=>$x)
  {
     $e='';
     if ($sub==$k) {$e=' checked="checked"';}
     $subj.='<input type="radio" name="bedStatus" id="allot" value="'.$k.'"'.$e.'>'.$x.'</br>';
  }
  $subj.='</div>';
  $r.='<form name="" action="" method="post"><input name="docomment" type="hidden" value="Reg3"><table>';
  $r.='<tr><td class="flabel">Субъект:</td><td>'.$subj.'</td>';
  $r.='<td class="flabel"><div id="name-label">Название компании:</div></td><td><input id="nm" class="ffieldinp" name="name" type="text" value="'.$name.'"></td></tr>';
  $r.='<tr><td class="flabel">Местоположение:</td><td><input class="ffieldinp" id="city" name="city" type="text" value="'.$city.'"><input id="id_city" name="id_city" type="hidden" value="'.$id_city.'"></td><td colspan="2"><font color="red">'.$err.'</font></td></tr>';
  $r.='<tr><td class="flabel">Адрес:</td><td><textarea class="ffieldinp" name="addr" style="height:80px;"  wrap="off">'.$addr.'</textarea></td></tr>';
  $r.='<tr><td class="flabel">E-Mail:</td><td><input class="ffieldinp" name="mail" type="text" value="'.$mail.'"></td>';
  $r.='<td class="flabel">Skype:</td><td><input class="ffieldinp" name="skype" type="text" value="'.$skype.'"></td></tr>';
  $r.='<tr><td class="flabel">Телефон 1:</td><td><input id="tel1" class="ffieldinp" name="tel1" type="text" value="'.$tel1.'"></td>';
  $r.='<td class="flabel">Телефон 2:</td><td><input id="tel2" class="ffieldinp" name="tel2" type="text" value="'.$tel2.'"></td></tr>';
  $r.='<tr><td class="flabel">Телефон 3:</td><td><input id="tel3" class="ffieldinp" name="tel3" type="text" value="'.$tel3.'"></td></tr>';
  $r.='<tr><td class="flabel"></td><td><input type="submit" value="Продолжить"></td></tr>';
  $r.='</table></form>';
  }

  return $r;
}
function DellaReg3($par)
{
  global $page;
  $r='';
  $r=NewCompany('',-1,'');

  return $r;
}
function DellaProfile ($par)
{
  global $page;
  $r='';
  $err='';
  if (isset($_POST['docomment'])&&$_POST['docomment']=="Profile")
  {
    $fio=mysql_real_escape_string(GetPostGetParam('fio'));
    $id_city=mysql_real_escape_string(GetPostGetParam('id_city'));
    $city=DellaGetCity($id_city);
    $tel1=mysql_real_escape_string(GetPostGetParam('tel1'));
    $tel2=mysql_real_escape_string(GetPostGetParam('tel2'));
    $tel3=mysql_real_escape_string(GetPostGetParam('tel3'));
    $skype=mysql_real_escape_string(GetPostGetParam('skype'));

    $sql='update t_contacts set fio="'.$fio.'",id_city="'.$id_city.'",tel1="'.$tel1.'",tel2="'.$tel2.'",tel3="'.$tel3.'",skype="'.$skype.'" where (id='.$page['user']['id'].')';
    $R = mysql_query_my ($sql) or die ("Error in Profile (1)<br>".mysql_error().'<br>'.$sql);

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

   $sql='select fio,id_city,tel1,tel2,tel3,mail,skype from t_contacts where (id='.$page['user']['id'].')';
   $R = mysql_query_my ($sql) or die ("Error in Profile (1)<br>".mysql_error().'<br>'.$sql);
   $T=mysql_fetch_array($R);
   $id_city=$T['id_city'];
   $city=DellaGetCity($id_city);
   $r.='<form name="" action="" method="post"><input name="docomment" type="hidden" value="Profile"><table>';
   $r.='<td class="flabel"><div id="name-label">ФИО:</div></td><td><input id="nm" class="ffieldinp" name="fio" type="text" value="'.$T['fio'].'"></td></tr>';
   $r.='<tr><td class="flabel">Местоположение:</td><td><input class="ffieldinp" id="city" name="city" type="text" value="'.$city.'"><input id="id_city" name="id_city" type="hidden" value="'.$id_city.'"></td><td colspan="2"><font color="red">'.$err.'</font></td></tr>';
   $r.='<tr><td class="flabel">E-Mail:</td><td><b>'.$T['mail'].'</b></td>';
   $r.='<td class="flabel">Skype:</td><td><input class="ffieldinp" name="skype" type="text" value="'.$T['skype'].'"></td></tr>';
   $r.='<tr><td class="flabel">Телефон 1:</td><td><input id="tel1" class="ffieldinp" name="tel1" type="text" value="'.$T['tel1'].'"></td>';
   $r.='<td class="flabel">Телефон 2:</td><td><input id="tel2" class="ffieldinp" name="tel2" type="text" value="'.$T['tel2'].'"></td></tr>';
   $r.='<tr><td class="flabel">Телефон 3:</td><td><input id="tel3" class="ffieldinp" name="tel3" type="text" value="'.$T['tel3'].'"></td></tr>';
   $r.='<tr><td class="flabel"></td><td><input type="submit" value="Сохранить"></td></tr>';
   $r.='</table></form>';


  return $r;
}


?>