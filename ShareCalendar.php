<?php
require_once "config.php";

if(isset($_SESSION['access_token'])){
	$authUrl = $gClient->createAuthUrl();
	$gClient->setAccessToken($_SESSION['access_token']);}
else if(isset($_GET['code'])){
	$token = $gClient->fetchAccessTokenWithAuthCode($_GET['code']);
	$_SESSION['access_token']=$token;
}
else 
{
	header('Location:LoginPage.php');
}

if(isset($_POST['submit'])){
	$service = new Google_Service_Calendar($gClient); 
	$rule = new Google_Service_Calendar_AclRule();
	$scope = new Google_Service_Calendar_AclRuleScope();
	$rule->sendNotifications=true;
	$scope->setType("user");
	$scope->setValue($_POST["email"]);
	$rule->setScope($scope);
	$rule->setRole("reader");
	$sendNotifications = array('sendNotifications' => true);
	$createdRule = $service->acl->insert('primary', $rule, $sendNotifications);
}

?>

<!Doctype html>
<html>
<head>
	<title>Share Calendar</title>
</head>

<body>
<ul>
	<li><a href="DashboardPage.php">Dashboard Page</a></li>
	<li><a href="LogoutPage.php">Sign out</a></li>
</ul>
	
<div id="shareCalendarForm">
  <form action="#" method="POST">
    <h1>Set User</h1>
	
    <label><b>Share user email address</b></label>
    <input type="email" name ="email" placeholder="Enter email address of a user" id="email address" required>
    <input type="submit" name="submit">
  </form>
</div>
	
</body>
</html>




