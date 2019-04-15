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
	$createdEvent = $service->events->insert('primary', $event);
}
?>

<!Doctype html>
<html>
<head>
	<title>Set Notification Page</title>
</head>

<body>
<ul>
	<li><a href="DashboardPage.php">Dashboard Page</a></li>
	<li><a href="LogoutPage.php">Sign out</a></li>
</ul>

<div id="reminderForm">
  <form action="#" method="POST">
    <h1>Set Reminder</h1>
	
    <label><b>Reminder Title</b></label>
    <input type="text" placeholder="Enter Reminder Title" name="title" required> <br>

    <label><b>Reminder Date</b></label>
    <input type="date" name="date"><br>
	
	<label><b>Reminder Time</b></label>
    <input type="time" name="time"><br>

    <input type="submit" name="submit">
  </form>
</div>
	
</body>
</html>

