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
<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Page</title>
    <!-- <link rel='stylesheet' href='css/general.css'/> -->
    <link rel='stylesheet' href='fullcalendar/fullcalendar.css' />
    <!-- <link href="//fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,700,700i" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">


    <script src='fullcalendar/lib/jquery.min.js'></script>
    <script src='fullcalendar/lib/moment.min.js'></script>
    <script src='fullcalendar/fullcalendar.js'></script>
    <script type='text/javascript' src='fullcalendar/gcal.js'></script>

    <script>
    $(function() {
        $('#calendar').fullCalendar({
            header: {
                left: "prev,next,today",
                center: "title",
                right: "month,listWeek"
            },
            eventSources: [{
                googleCalendarApiKey: "AIzaSyCH4g3WsOSKOy5gUFQu-A71MrHlvtgxxgQ",
                googleCalendarId: "<?php echo $userData['email']?>"
            }]

        })
    });
    </script>




</head>

<body>
    <ul class="nav justify-content-end">
        <!-- <li class="nav-item">
            <a id="addButton" class="nav-link" href="AddEvent.php">Add Event</a>
        </li> -->
        <li class="nav-item">
            <a id="chartButton" class="nav-link" href="ChartPage.php">Rush Level</a>
        </li>
        <li class="nav-item">
            <a id="logoutButton" class="nav-link btn btn-danger" href="LogoutPage.php">Sign Out</a>
        </li>
    </ul>

    <div class="container">
        <div id="calendar"></div>
    </div>
    <!-- <div class="col-md-5">
        <canvas id="myChart"></canvas>
    </div> -->


    <!-- <canvas id="myChart" width="400" height="400"></canvas> -->


</body>

</html>