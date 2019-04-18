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
	$createdEvent = $service->events->insert('primary', $event);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add an Event</title>

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

    <div id="addEventForm">
        <form action="#" method="POST">
            <h1>Add Event</h1>

            <label><b>Title</b></label>
            <input type="text" placeholder="Enter Event Title" name="title" required> <br>

            <label><b>Date</b></label>
            <input type="date" name="date"><br>

            <label><b>Time</b></label>
            <input type="time" name="time"><br>

            <input type="submit" name="submit">
        </form>
    </div>

</body>

</html>