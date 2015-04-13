<?php
function FacebookAuth($par)
{  global $facebookuser,$facebookuser_profile,$facebookuser_mail;
  require 'fb-sdk/src/facebook.php';
  $facebook = new Facebook(array(
  'appId'  => '539259076111259',
  'secret' => '66abcb85c57c9e66099cdc2b9567aad6',
  ));

  $facebookuser = $facebook->getUser();               //http://localhost/fb

if ($facebookuser) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $facebookuser_profile = $facebook->api('/me');
    $facebookuser_mail = $facebook->api('/me?fields=email');
    $user_frends = $facebook->api('/me/friends');

  } catch (FacebookApiException $e) {                 http://localhost/
    error_log($e);
    $facebookuser = null;
  }
}

if ($facebookuser) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl();
 }
if ($facebookuser) {
 $r='<img src="https://graph.facebook.com/'.$facebookuser_profile['username'].'/picture" align="right">'.$facebookuser_profile['name'];
} else {$r='<a href="'.$loginUrl.'">LogIn</a>';}

return $r;
}
?>
