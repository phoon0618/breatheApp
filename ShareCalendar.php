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
	$rule->setRole("freeBusyReader");
	$sendNotifications = array('sendNotifications' => true);
	$createdRule = $service->acl->insert('primary', $rule, $sendNotifications);?>
	
	<script>alert("Successfully shared your calendar")</script> <?php
}

?>

<!Doctype html>
<html>
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Share Calendar Page</title>
	
	<!-- Navigation Css -->
	<link rel="stylesheet" type="text/css" href='../css/nav.css'>
	<link rel="stylesheet" type="text/css" href='../css/form.css'>
</head>

<body>
<ul class="nav">
	<li class="title">Share Calendar Page</li>
	<li><a href="LogoutPage.php">Sign out</a></li>
	<li><a href="DashboardPage.php">Dashboard Page</a></li>
</ul>
	
<div id="shareCalendarForm">
  <form action="#" method="POST">
    <h1 class="title">Share Calendar</h1>
	
    <label><b>Email address</b></label>
    <input type="email" name ="email" placeholder="Enter email address" required>
	<br>
   <input type="submit" name="submit" class="submit_btn">
  </form>
</div>
	
</body>
</html>




