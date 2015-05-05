<?php
require_once "inc/src/whatsprot.class.php";
$username = "77479677516"; //Mobile Phone prefixed with country code so for india it will be 91xxxxxxxx
$password = "5MMxnWI7boC0Y3Mnn+jf8zIMSSc=";

$w = new WhatsProt($username, 0, "Repa Inc.", true); //Name your application by replacing
echo "Connect ";
$w->connect();
echo "Ok<br>";
echo "Login";
$w->loginWithPassword($password);
echo "Ok<br>";
$target = "77015578624"; //Target Phone,reciever phone
//$target = "77015578624"; //Target Phone,reciever phone
//$message = 'Hi from repa! <a href="http://discoveery.ru/">Дисковери</a>';

$w->SendPresenceSubscription($target); //Let us first send presence to user
$w->sendMessage($target,$message ); // Send Message
$filepath='1.jpg';
$fsize='';
$fhash='';
$caption='Салам, сообщение отправленное моим ботом. Ответ суда не пиши, ответы не читает... ;)';
$w->sendMessageImage($target, $filepath, false, $fsize, $fhash, $caption);
echo "Message Sent Successfully";
?>