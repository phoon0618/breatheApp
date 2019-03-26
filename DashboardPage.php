<?php 
require_once "config.php";

if(isset($_SESSION['access_token']))
    $gClient->setAccessToken($_SESSION['access_token']);
else if(isset($_GET['code'])){
    $token = $gClient->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token']=$token;
}
else{
    header('Location:LoginPage.php');
}

// Get the API client and construct the service object.
$oAuth = new Google_Service_Oauth2($gClient);
$service = new Google_Service_Calendar($gClient);
$userData =	$oAuth->userinfo_v2_me->get();

$calendarId = 'primary';

//get this week events
$optParams = array(
    'orderBy' => 'startTime',
    'singleEvents' => true,
    'timeMin' => getMinDate(),
    'timeMax' => getMaxDate()
);

//variable declarations
$sleepHours = 8;
$stressLevelsBusy= array();
$stressLevelsFree= array();

$results = $service->events->listEvents($calendarId, $optParams);
$events = $results->getItems();

//generate datasets for charts
if (!empty($events)) {
    $dayCount = 0; //represent sunday
    $cur = getMinDate();//initialise cursor to check if previous event is on same day
    for($dayCount = 0; $dayCount < 7; $dayCount++){
        $durationBusy = 0;
        foreach ($events as $event) {
            $start = $event->start->dateTime;
            $end = $event->end->dateTime;
            $sameDay = getDayOfWeekNumber($start) == $dayCount? True: False;

            if (empty($start))
                $start = $event->start->date;
            if (empty($end))
                $end = $event->end->date;
            
            $start = strtotime($start);
            $end = strtotime($end);
            
            if($sameDay){
                $durationBusy += ($end - $start) / 3600; //in hours
            }
        };
        
        $durationFree = 24 - $durationBusy - $sleepHours;
        
        array_push($stressLevelsBusy, $durationBusy);
        array_push($stressLevelsFree, $durationFree);
    }
}

function getDayOfWeekFromDate($date){
    return date("l",strtotime($date));
}

//so that will save 0 for days without events
function getDayOfWeekNumber($date){
    $date = getDayOfWeekFromDate($date);
    //switch statement for sun = 0, mon = 1, etc
    switch ($date) {
        case 'Sunday':
            return "0";
        
        case 'Monday':
            return "1";
        
        case 'Tuesday':
            return "2";
        
        case 'Wednesday':
            return "3";
    
        case 'Thursday':
            return "4";
        
        case 'Friday':
            return "5";
        
        case 'Saturday':
            return "6";
        
        
    }
}

//get first day of the week
function getMinDate(){
    $minDate = (date("l",strtotime("now")) === "Sunday") ? 
        strtotime("now") : //if today is sunday, today is first day of week
        strtotime("previous sunday");
    return date('c', $minDate);
}

//get last day of the week
function getMaxDate(){
    $maxDate = (date("l",strtotime("now")) === "Saturday") ?
        strtotime("now"): //if today is saturday, today is last day of week
        strtotime("next saturday");
    return date('c', $maxDate);
}

?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard Page</title>
    <link rel='stylesheet' href='fullcalendar/fullcalendar.css' />

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js'>
    </script>

    <style type="text/css">
    #chart-container {
        width: 100%;
        height: auto;
    }
    </style>

    <!-- Calendar Scripts -->
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
                right: "month, listWeek"
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
            <a id="chartButton" class="nav-link" href="HelpPage.php">How to Use</a>
        </li> -->
        <li class="nav-item">
            <a id="logoutButton" class="nav-link btn btn-danger" href="LogoutPage.php">Sign Out</a>
        </li>
    </ul>

    <div id="content"></div>
    <div class="container">
        <div id="calendar"></div>
    </div>

    <div class="container">
        <div id="chart-container">
            <canvas id="graphCanvas"></canvas>
        </div>
    </div>

    <!-- rush level scripts -->
    <script>
    $(document).ready(function() {
        showGraph();
    });

    function showGraph() {
        {
            $.post("DashboardPage.php",
                function(data) {

                    var xAxis = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                    var dsBusy = [];
                    var dsFree = [];

                    <?php foreach($stressLevelsBusy as $sl){ ?>
                    dsBusy.push(<?php echo $sl?>);
                    <?php } ?>

                    <?php foreach($stressLevelsFree as $sl){ ?>
                    dsFree.push(<?php echo $sl?>);
                    <?php  } ?>

                    var chartdata = {
                        labels: xAxis,
                        datasets: [{
                            label: 'Busy',
                            backgroundColor: '#ff49e260',
                            borderColor: '#f146d580',
                            data: dsBusy
                        }, {
                            label: 'Free',
                            backgroundColor: '#49e2ff60',
                            borderColor: '#46d5f180',
                            data: dsFree
                        }]
                    };

                    var options = {
                        title: {
                            display: true,
                            text: 'Weekly Stress Level',
                        },
                        scales: {
                            ticks: {
                                beginAtZero: true
                            }
                        }
                    };

                    var graphTarget = $("#graphCanvas");

                    var barGraph = new Chart(graphTarget, {
                        type: 'radar',
                        data: chartdata,
                        options: options
                    });
                });
        }
    }
    </script>
</body>

</html>