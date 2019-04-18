<?php
require_once "config.php";

if(isset($_SESSION['access_token'])){
	$authUrl = $gClient->createAuthUrl();
	$gClient->setAccessToken($_SESSION['access_token']);}
else if(isset($_GET['code'])){
	$token = $gClient->fetchAccessTokenWithAuthCode($_GET['code']);
	$_SESSION['access_token']=$token;
}
else{
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

<!DOCTYPE html>
<html>

<head>
    <title>Share Calendar</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- https://getbootstrap.com/docs/4.0/components/modal/ -->

</head>

<body>
    <ul class="nav justify-content-end">
        <li class="nav-item p-2">
            <a class="nav-link btn btn-primary" href="DashboardPage.php">Dashboard Page</a>
        </li>
        <li class="nav-item p-2">
            <a class="nav-link btn btn-danger" href="LogoutPage.php">Sign Out</a>
        </li>
    </ul>

    <div id="shareCalendarForm">
        <form action="#" method="POST">
            <h1>Set User</h1>

            <label><b>Share user email address</b></label>
            <input type="email" name="email" placeholder="Enter email address of a user" id="email address" required>
            <input type="submit" name="submit">
        </form>
    </div>

</body>

</html>