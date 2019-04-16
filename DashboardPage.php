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

	$oAuth = new Google_Service_Oauth2($gClient);
	$userData =	$oAuth->userinfo_v2_me->get();
	$service = new Google_Service_Calendar($gClient);
	
   // get event from the google calendar
  $calendarId = 'primary';
  $eventDay = array(
    'orderBy' => 'startTime',
    'singleEvents' => TRUE,
	'timeMax' => date('c', mktime(0,0,0, date('m'), date('d')+1, date('y'))),
    'timeMin' => date('c'),
  );
  
  $results = $service->events->listEvents($calendarId, $eventDay);
  $durationBusyHours=0;
  $event_details=array();	
  $work = 0;
  $recretional = 0;

  if (count($results->getItems()) != 0) {
    foreach ($results->getItems() as $event) {
      $category = $event->getDescription();
	  $title = $event ->getsummary();
      $start = $event->start->dateTime;
	  $end = $event->end->dateTime;
        
    if($category=="Work"){
        $work+=1;
    }

    if($category=="Recreational"){
        $recretional+=1;
    }

      if (empty($start)) {
        $start = $event->start->date;
      }
	  if(empty($end))
	  {
		 $end = $event->end->date;  
	  }

      $startEvent = strtotime($start);
      $endEvent = strtotime($end);
	  $durationBusyHours += ($endEvent - $startEvent)/3600; //in hours
	  
	  $start = new DateTime($start);
	  $end = new DateTime($end);
	  array_push($event_details,array("title"=>$title,"startTime"=>$start->format('H:i:s'),"endTime"=>$end->format('H:i:s'))); 
    } 
  }
   
   
   	//get this week events
$eventWeek = array(
    'orderBy' => 'startTime',
    'singleEvents' => true,
    'timeMin' => getMinDate(),
    'timeMax' => getMaxDate()
);

//variable declarations
$sleepHours = 8;
$stressLevelsBusy= array();
$stressLevelsFree= array();

$results = $service->events->listEvents($calendarId, $eventWeek);
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

if($recretional<$work){
    ?>
<script type="text/javascript" src="https://code.jquery.com/jquery-1.8.2.js"></script>
<link rel="stylesheet" type="text/css" href="style.css">
<script type='text/javascript'>
$(function() {
    var overlay = $('<div id="overlay"></div>');
    overlay.show();
    overlay.appendTo(document.body);
    $('.popup').show();
    $('.close').click(function() {
        $('.popup').hide();
        overlay.appendTo(document.body).remove();
        return false;
    });



    $('.x').click(function() {
        $('.popup').hide();
        overlay.appendTo(document.body).remove();
        return false;
    });
});
</script>
<div class='popup'>
    <div class='cnt223'>
        <h1>“The unexamined life is not worth living” </h1>
        <p>
            – Socrates.
            <br />
            <br />
            <a href='' class='close'>Close</a>
        </p>
    </div>
</div>
<?php
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

<!Doctype html>
<html>

<head>
    <title>Dashboard Page</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <style type="text/css">
    #chart-container {
        width: 100%;
        height: auto;
    }
    </style>

    <!-- Chart Scripts -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js'>
    </script>

    <!-- Calendar Scripts -->
    <link rel='stylesheet' href='../fullcalendar/fullcalendar.css' />
    <script src='../fullcalendar/lib/jquery.min.js'></script>
    <script src='../fullcalendar/lib/moment.min.js'></script>
    <script src='../fullcalendar/fullcalendar.js'></script>
    <script src='../fullcalendar/gcal.js'></script>
    <script src='../node_modules/push.js/bin/push.min.js'></script>


    <script>
    $(function() {
        $('#calendar').fullCalendar({
            plugins: ['googleCalendar'],
            header: {
                left: "prev,next today myCustomButton",
                center: "title",
                right: "month,listMonth,listWeek,listDay"
            },
            buttonText: {
                month: 'Month',
                listMonth: 'List Month',
                listYear: 'List Year',
                listWeek: 'List Week',
                listDay: 'List Day'
            },
            selectable: true,
            selectHelper: true,
            editable: true,
            droppable: true,

            eventSources: [{
                googleCalendarApiKey: "AIzaSyCH4g3WsOSKOy5gUFQu-A71MrHlvtgxxgQ",
                googleCalendarId: "<?php echo $userData['email']?>"
            }],

            eventClick: function(event) {

            },

            dayClick: function() {

            },


        })
    });

    function reminder() {
        Push.create("Take a break", {
            body: "Your busy time is more 8 hours",
            timeout: 6000,
        });
    }

    // reminder to drink water every 1 hours
    var i = 0;
    var timer = setInterval(function() {
        Push.create("Hydrate reminder", {
            body: "It's time to drink some water!",
            icon: '/icon.png',
            timeout: 4000,

            onClick: function() {
                window.focus();
                this.close();
            }
        });
    }, 3600000);


    $(document).ready(function() {
        setInterval(timestamp, 1000);

    });

    function timestamp() {
        $.ajax({
            url: 'http://localhost/breatheApp/pages/timestamp.php',
            success: function(data) {
                $('#timestamp').html(data);

                <?php foreach($event_details as $notification){?>
                if (data == '<?=$notification['startTime']?>') {
                    Push.create('<?=$notification['title']?>', {
                        body: '<?=$notification['startTime']?>' + " ~ " +
                            '<?=$notification['startTime']?>',
                        timeout: 6000,
                    });
                }
                <?php } ?>

            },
        });
    }
    </script>

</head>

<body>
    <!-- Navigation bar -->
    <ul class="nav justify-content-end">
        <li class="nav-item p-2">
            <a class="nav-link btn btn-primary" href="ReminderPage.php">Set Reminder</a>
        </li>
        <li class="nav-item p-2">
            <a class="nav-link btn btn-primary" href="ShareCalendar.php">Share Calendar</a>
        </li>
        <li class="nav-item p-2">
            <a class="nav-link btn btn-danger" href="LogoutPage.php">Sign Out</a>
        </li>
    </ul>

    <!-- Content -->
    <div id="timestamp"></div>

    <div id="content"></div>
    <div class="container">
        <div id="calendar"></div>
    </div>

    <div class="container">
        <canvas id="graphCanvas"></canvas>
    </div>
    </div>

    <?php if($durationBusyHours>8){
		echo "<script> reminder();</script>";
	}?>

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