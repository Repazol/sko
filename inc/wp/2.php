<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>WatsUP</title>
</head>
<body>

<?php
require_once "inc/src/whatsprot.class.php";
$username = "77479677516"; //Mobile Phone prefixed with country code so for india it will be 91xxxxxxxx
$password = "5MMxnWI7boC0Y3Mnn+jf8zIMSSc=";

function onGetProfilePicture($from, $target, $type, $data)
{
    if ($type == "preview") {
        $filename = "pictures/preview_" . $target . ".jpg";
    } else {
        $filename = "pictures/" . $target . ".jpg";
    }
    $fp = @fopen($filename, "w");
    if ($fp) {
        fwrite($fp, $data);
        fclose($fp);
    }
   $msg = "<img src='$filename' /></a>";
   echo "$msg<br>";
}

function onGetImage($mynumber, $from, $id, $type, $t, $name, $size, $url, $file, $mimetype, $filehash, $width, $height, $preview)
{
    //save thumbnail
    $previewuri = "media/thumb_" . $file;
    $fp = @fopen($previewuri, "w");
    if ($fp) {
        fwrite($fp, $preview);
        fclose($fp);
    }

    //download and save original
    $data = file_get_contents($url);
    $fulluri = "media/" . $file;
    $fp = @fopen($fulluri, "w");
    if ($fp) {
        fwrite($fp, $data);
        fclose($fp);
    }

    //format message
    $msg = "$name:<br><a href='$fulluri' target='_blank'><img src='$previewuri' /></a>";
    echo "$msg<br>$t:<i>$from</i><br>";
}

function onMessage($mynumber, $from, $id, $type, $time, $name, $body)
{
    echo "$name:$body<br>$time:<i>$from</i><br>";

}

$w = new WhatsProt($username,  "Repa Inc.", false); //Name your application by replacing
$w->eventManager()->bind("onGetImage", "onGetImage");
$w->eventManager()->bind("onGetProfilePicture", "onGetProfilePicture");
$w->eventManager()->bind("onGetMessage", "onMessage");

echo "Connect ";
$w->connect();
echo "Ok<br>";
echo "Login ";
$w->loginWithPassword($password);
echo "Ok<br>";
$w->sendGetProfilePicture($username, false);

$target = "77015578624"; //Target Phone,reciever phone
/*    while($w->pollMessage());
    $messages = $w->getMessages();
    if (count($messages) > 0) {
        foreach ($messages as $message) {
            $data = @$message->getChild("body")->getData();
            $ph = $message->getAttribute("notify");
     	    $fr= explode ("@",$message->getAttribute("from"));
     	    if (isset($fr[0])) {$ph.='('.$fr[0].')';}
            if ($data != null && $data != '') {
                $inbound[] = $data;
                echo "$ph : $data</br>";
            }
        }
    }

echo "Message Sent Successfully";
*/
  //for ($i=0;$i<150;$i++)
	while($w->pollMessage());

echo "Done...";
//$w->sendSetProfilePicture("2.jpg");
?>