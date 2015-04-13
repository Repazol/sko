<?php

function TryAuthUser($ses)
{ $u=array();
 $u['id']=-1;
 $u['profile']=$pr;
 //echo "<pre>"; print_r($ses); echo "</pre>";
 $pr=$ses['profile'];
 $u['identity']=mysql_real_escape_string($pr->identity);
 $u['provider']=mysql_real_escape_string($pr->provider);
 $R = mysql_query_my ('select id_user from cam_auth where (identity="'.$u['identity'].'")') or die ("Error in TryAuthUser<br>".mysql_error());
 $T=mysql_fetch_array($R);
 if (isset($T['id_user'])) {$u['id']=$T['id_user'];}
 return $u;
}
function SiteAuth($par)
{ global $page, $cam_user;
 //unset($cam_user); session_start();
 require_once 'loginza/LoginzaAPI.class.php';
 require_once 'loginza/LoginzaUserProfile.class.php';

 $page["head"]='<script src="http://loginza.ru/js/widget.js" type="text/javascript"></script>';
 $r='';

// объект работы с Loginza API
 $LoginzaAPI = new LoginzaAPI();

if (!empty($_POST['token'])) {
	// получаем профиль авторизованного пользователя
	$UserProfile = $LoginzaAPI->getAuthInfo($_POST['token']);

	// проверка на ошибки
	if (!empty($UserProfile->error_type)) {
		// есть ошибки, выводим их
		// в рабочем примере данные ошибки не следует выводить пользователю, так как они несут информационный характер только для разработчика
		$r.=$UserProfile->error_type.": ".$UserProfile->error_message;
	} elseif (empty($UserProfile)) {
		// прочие ошибки
		$r.='Temporary error.';
	} else {
		// ошибок нет запоминаем пользователя как авторизованного
		$_SESSION['loginza']['is_auth'] = 1;
		// запоминаем профиль пользователя в сессию или создаем локальную учетную запись пользователя в БД
		$_SESSION['loginza']['profile'] = $UserProfile;
	}
} elseif (isset($_GET['quit'])) {
	// выход пользователя
	unset($_SESSION['loginza']);
}

// проверка авторизации, вывод профиля если пользователь авторизован ранее
if (!empty($_SESSION['loginza']['is_auth'])) {

	// объект генерации недостаюих полей (если требуется)
	$LoginzaProfile = new LoginzaUserProfile($_SESSION['loginza']['profile']);

	// пользователь уже прошел авторизацию
	$avatar = '';
	if (!empty($_SESSION['loginza']['profile']->photo)) {
		$avatar = '<img src="'.$_SESSION['loginza']['profile']->photo.'" style="max-height:30px;" align="right"/> ';
	}

	$r1=$avatar.'<a style="padding: 1em 0.1em;" href="log">'.$LoginzaProfile->genDisplayName().'</a>, <a style="padding: 1em 0.1em;" href="?quit">LogOut </a>';
    $r='<ul id="auth" class="menu" style="color:gray1; background:#3b5998;border:0;height:50px;"><li><a href="" style="color:darkblue;text-shadow:0px 0px 0px #fff;">'.$LoginzaProfile->genDisplayName().'</a>';
	$r.='<ul style="display:none;">	<li><a href="log" style="color:gray;"><i class="icon-cog"></i> Settings</a></li>';
	$r.='<li><a href="?quit" style="color:gray;"><i class="icon-cog"></i> Logout</a></li>';
	$r.='</ul></li></ul>';
	$page['loginza']='';
	$page['loginza'].="<h3>Приветствуем Вас:</h3>";
	$page['loginza'].=$avatar . $LoginzaProfile->genDisplayName().', <a href="?quit">LogOut ('.$LoginzaProfile->genNickname().')</a>';
	$page['loginza'].="<p>";
	$page['loginza'].="Ник: ".$LoginzaProfile->genNickname()."<br/>";
	$page['loginza'].="Отображать как: ".$LoginzaProfile->genDisplayName()."<br/>";
	$page['loginza'].="Полное имя: ".$LoginzaProfile->genFullName()."<br/>";
	$page['loginza'].="Сайт: ".$LoginzaProfile->genUserSite()."<br/>";
	$page['loginza'].="</p>";
	$page['loginza'].=$LoginzaAPI->debugPrint($_SESSION['loginza']['profile']);

	//Проверяем первый ли вход в систему...
	$cam_user=TryAuthUser($_SESSION['loginza']);
	$cam_user['fio']=$LoginzaProfile->genDisplayName();
	$cam_user['avatar']=$_SESSION['loginza']['profile']->photo;
	if (isset($_SESSION['loginza']['profile']->email)) {$cam_user['mail']=$_SESSION['loginza']['profile']->email;}
	$page['loginza'].='<pre>'.print_r($cam_user,true).'</pre>';
	if ($cam_user['id']==-1&&$page['link']!='Registration') { //RedirectTo Register	  header('Location: Registration');
	  exit;	}

} else {
	// требуетс авторизация, вывод ссылки на Loginza виджет
	$r='<a href="'.$LoginzaAPI->getWidgetUrl().'&providers_set=google,facebook&lang=en" class="loginza">LogIn</a>';

}
 return $r;
}

?>