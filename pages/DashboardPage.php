<?php 

require_once "config.php";

if(isset($_SESSION['access_token']))
	$gClient->setAccessToken($_SESSION['access_token']);
else if(isset($_GET['code'])){
	$token = $gClient->fetchAccessTokenWithAuthCode($_GET['code']);
	$_SESSION['access_token']=$token;
}
else 
{
	header('Location:LoginPage.php');
}

	$oAuth = new Google_Service_Oauth2($gClient);
	$userData =	$oAuth->userinfo_v2_me->get();

?>
<!Doctype html>
<html>
<head>
	<title>Dashboard Page</title>
	<link rel='stylesheet' href='fullcalendar/fullcalendar.css'/>
	<script src='fullcalendar/lib/jquery.min.js'></script>
	<script src='fullcalendar/lib/moment.min.js'></script>
	<script src='fullcalendar/fullcalendar.js'></script>
	<script type='text/javascript' src='fullcalendar/gcal.js'></script>
	<script >
		$(function() {
		  $('#calendar').fullCalendar({
			header:{
				  left:"prev,next,today",
				  center:"title",
				  right:"month,listDay"
			  },
			eventSources: [
			{
				googleCalendarApiKey: "AIzaSyCH4g3WsOSKOy5gUFQu-A71MrHlvtgxxgQ",
				googleCalendarId: "<?php echo $userData['email']?>"
			}]
				  
		  })
		});
	</script>
</head>

<body>
	<a href="LogoutPage.php">Logout</a>
	<div id="calendar"></div>
</body>
</html>

