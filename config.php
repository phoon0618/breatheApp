<?php 
	session_start();
	require_once("vendor/autoload.php");
	$gClient = new Google_Client();
	$gClient->setClientId("704635491573-i43kdthr8juuanmbcjrh2k2jlj6lgfo7.apps.googleusercontent.com");
	$gClient->setClientSecret("GnApCiTjZSgBKaokyNYJaG3C");
	$gClient->setApplicationName("breatheApp");
	$gClient->setRedirectUri("http://localhost/breatheApp/DashboardPage.php");
	$gClient->addScope("https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email");
?>







