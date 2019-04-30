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
	date_default_timezone_set('Asia/Kuala_Lumpur');
	$service = new Google_Service_Calendar($gClient);
	$event = new Google_Service_Calendar_Event();
	$event->setSummary($_POST["title"]);
	$start = new Google_Service_Calendar_EventDateTime();
	$startEvent = new DateTime($_POST["date"]." ".$_POST["time"]);
	$start->setDateTime($startEvent->format('c'));
	$event->setStart($start);
	$end = new Google_Service_Calendar_EventDateTime();
	$endEvent = strtotime($_POST["date"]." ".$_POST["time"])+1800;
	$end->setDateTime(date('c',$endEvent));
	$event->setEnd($end);
	$createdEvent = $service->events->insert('primary', $event);?>
	
	<script>alert("Successfully set your reminder")</script> <?php
}
?>

<!DOCTYPE html>
<html>

<head>
	<title>Customise Notification Page</title>
	
	<!-- Navigation Css -->
	<link rel="stylesheet" type="text/css" href='../css/nav.css'>
	<link rel="stylesheet" type="text/css" href='../css/form.css'>
</head>

<body>
<ul class="nav">
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<li class="title" >Customise Notification Page</li>
	<li><a href="LogoutPage.php">Sign out</a></li>
	<li><a href="DashboardPage.php">Dashboard Page</a></li>
	
</ul>

<div id="reminderForm">
  <form action="#" method="POST">
    <h1 class="title">Set Reminder</h1>
	
    <label><b>Reminder Title</b></label>
    <input type="text" placeholder="Enter Reminder Title" name="title" required> <br>

            <input type="submit" name="submit">
        </form>
    </div>

    <input type="submit" name="submit" class="submit_btn">
	
  </form>
</div>
	
</body>

</html>