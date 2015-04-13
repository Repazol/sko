<?php 
     define('_SAPE_USER', 'e8ba3a1e2e2dddd6c1ac64cbcca3e116');
     require_once($_SERVER['DOCUMENT_ROOT'].'/'._SAPE_USER.'/sape.php'); 
     $sape_articles = new SAPE_articles();
     echo $sape_articles->process_request();
?>
