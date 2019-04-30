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
	$event->setDescription($_POST["event_category"]);
	$start = new Google_Service_Calendar_EventDateTime();
	$startEvent = new DateTime($_POST["startDate"]." ".$_POST["startTime"]);
	$start->setDateTime($startEvent->format('c'));
	$event->setStart($start);
	$end = new Google_Service_Calendar_EventDateTime();
	$endEvent = new DateTime($_POST["endDate"]." ".$_POST["endTime"]);
	$end->setDateTime($endEvent->format('c'));
	$event->setEnd($end);
	$attendee1 = new Google_Service_Calendar_EventAttendee();
	if(!empty($_POST["email"])){
		$attendee1->setEmail($_POST["email"]);
		$attendees = array($attendee1,
                   // ...
                  );
		$event->attendees = $attendees;
	}
	$sendNotifications = array('sendNotifications' => true);
	$createdEvent = $service->events->insert('primary', $event, $sendNotifications);
?>	
	<script>alert("Successfully add the event")</script>
	<?php
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add an Event</title>
	
	<!-- Navigation Css -->
    <link rel="stylesheet" type="text/css" href='../css/nav.css'>
	<link rel="stylesheet" type="text/css" href='../css/form.css'>
</head>

<body>
    <ul class="nav">
		<li class="title">Add Event Page</li>
		<li><a  href="LogoutPage.php">Sign Out</a></li>
		<li><a  href="DashboardPage.php">Dashboard Page</a></li>
    </ul>

    <div id="addEventForm">
        <form action="#" method="POST">
            <h1 class="title">Add Event</h1>

            <label><b>Title</b></label><br>
            <input type="text" placeholder="Enter Event Title" name="title" required> <br>
			
			<label><b>Category</b></label><br>
			<select name="event_category" class="category">
				<option value="Work">Work</option>
				<option value="Exercise">Exercise</option>
				<option value="Recreational">Recreational</option>
			</select><br>

            <label><b>Start Date</b></label><br>
            <input type="date" name="startDate"  required><br>
			
			<label><b>Start Time</b></label><br>
            <input type="time" name="startTime"  required><br>
			
			<label><b>End Date</b></label><br>
            <input type="date" name="endDate"  required><br>
          
			<label><b>End Time</b></label><br>
            <input type="time" name="endTime"  required><br>
			
			 <label><b>Invite Family/Friend</b></label><br>
			 <input type="email" name ="email" placeholder="Enter email address">
			 

            <input type="submit" name="submit" class="submit_btn">
        </form>
    </div>

</body>

</html> 
