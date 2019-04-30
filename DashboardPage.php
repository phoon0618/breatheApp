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
	
   // get daily event from the google calendar
  $calendarId = 'primary';
  $eventDay = array(
    'orderBy' => 'startTime',
    'singleEvents' => TRUE,
	'timeMax' => date('c', mktime(0,0,0, date('m'), date('d')+1, date('y'))),
    'timeMin' => date('c', mktime(0,0,0, date('m'), date('d'), date('y'))),
	'timeZone' => 'Asia/Kuala_Lumpur',
  );
  
  $results = $service->events->listEvents($calendarId, $eventDay);
  //declare viarables
  $sleepHours = 8; //asume user sleep 8 hours
  $durationBusyHours = 0;
  $durationFreeHours = 0;
  $durationWork = 0;
  $durationRecretional = 0;
  $durationExercise = 0;
  $event_details = array();	

  if (count($results->getItems()) != 0) {
    foreach ($results->getItems() as $event) {
	  $title = $event ->getsummary();
	  $category = $event->getDescription();
      $start = $event->start->dateTime;
	  $end = $event->end->dateTime;
    
	if (empty($start)) {
		$start = $event->start->date;
    }
	
	if(empty($end))
	{
		$end = $event->end->date;  
	}  
	
	//calculate busy hours per day
	$startEvent = strtotime($start);
    $endEvent = strtotime($end);
	$durationBusyHours += ($endEvent - $startEvent)/3600; //in hours
	
	if($category=="Work"){
        $durationWork += ($endEvent - $startEvent)/3600; 
    }
	
	if($category=="Exercise"){
       $durationExercise += ($endEvent - $startEvent)/3600; 
    }
	
	if($category=="Recreational"){
       $durationRecretional += ($endEvent - $startEvent)/3600; 
    }	
	  
	//for notification purpose
	$start = new DateTime($start);
	$end = new DateTime($end);
	array_push($event_details,array("title"=>$title,"startTime"=>$start->format('H:i:s'),"endTime"=>$end->format('H:i:s'))); 
    }
	$durationBusyHours = $durationExercise + $durationWork + $durationRecretional;
	$durationFreeHours = 24-8-$durationBusyHours; //in hours
  }

	//get this weekly events
	$eventWeek = array(
		'orderBy' => 'startTime',
		'singleEvents' => true,
		'timeMin' => getMinDate(),
		'timeMax' => getMaxDate()
	);

	//variable declarations
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

  if($durationRecretional+$durationExercise<$durationWork){
    ?>
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.8.2.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
        <script type='text/javascript'>
                $(function(){
                var overlay = $('<div id="overlay"></div>');
                overlay.show();
                overlay.appendTo(document.body);
                $('.popup').show();
                $('.close').click(function(){
                $('.popup').hide();
                overlay.appendTo(document.body).remove();
                return false;
                });

                $('.x').click(function(){
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
                <br/>
                <br/>
                <a href='' class='close'>Close</a>
                </p>
                </div>
                </div>
    <?php
}
   
	if(isset($_POST['sleepHours']))
	{
		$sleepHours = $_POST["sleepingHours"];
	}

?>

<!Doctype html>
<html>
<head>
	<title>Dashboard Page</title>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<!-- Chart Scripts -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js'>
    </script>	
	
	<!-- Navigation Css -->
	<link rel="stylesheet" type="text/css" href='../css/nav.css'>
	<link rel="stylesheet" type="text/css" href='../css/calendar.css'>
	
	<!-- Calendar Scripts -->
	<link rel='stylesheet' href='../fullcalendar/fullcalendar.css'/>
	<script src='../fullcalendar/lib/jquery.min.js'></script>
	<script src='../fullcalendar/lib/moment.min.js'></script>
	<script src='../fullcalendar/fullcalendar.js'></script>
	<script src='../fullcalendar/gcal.js'></script>	
	<script src='../node_modules/push.js/bin/push.min.js'></script>
	
	
	<script>
		$(function() {
		  $('#calendar').fullCalendar({
			plugins: [ 'googleCalendar' ],
			header:{
				  left:"prev,next today myCustomButton",
				  center:"title",
				  right:"month,listMonth,listWeek,listDay"
			  },
			  buttonText: {
				month: 'Month',
				listMonth: 'List Month',
				listYear: 'List Year',
				listWeek: 'List Week',
				listDay: 'List Day'
			},
			  selectable:true,
			  selectHelper:true,
			  editable:true,
			  droppable: true, 

			eventSources: [
			{
				googleCalendarApiKey: "AIzaSyCH4g3WsOSKOy5gUFQu-A71MrHlvtgxxgQ",
				googleCalendarId: "<?php echo $userData['email']?>"			
			}],
			
		  })
		});
		
		function reminder(){
			Push.create("Take a break",{
				body: "Your busy time is more 8 hours" ,
				timeout:6000,
			});
		}
		
	// reminder to drink water every 1 hours
    var i = 0;
    var timer = setInterval(function() {
        Push.create("Hydrate reminder", {
		body: "It's time to drink some water!",
		icon: '/icon.png',
		timeout: 4000,
		   
		onClick: function () {
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
				if(data == '<?=$notification['startTime']?>'){
					Push.create('<?=$notification['title']?>',{
						body: '<?=$notification['startTime']?>'+" ~ "+'<?=$notification['startTime']?>' ,
						timeout:6000,
					});
				}
				<?php } ?>
				
			},
		});
	}	
	
	function chart(){
		 var chart_btn = document.getElementById("chart_btn").value;
		 var pieChart = document.getElementById("piechart");
		 var radarChart = document.getElementById("radarchart");
		
			if (chart_btn == "Weekly Stress Level" )
			{
				radarChart.style.display = "block";
				pieChart.style.display = "none";
				document.getElementById("chart_btn").value ="Daily Activities";
			}
			else {
				radarChart.style.display = "none";
				pieChart.style.display = "block";
				document.getElementById("chart_btn").value ="Weekly Stress Level" ;
			}
		}
	</script>
</head>

<body>
<!-- Navigation bar -->	
<ul class="nav">
	<li class="title">Dashboard Page</li>
	<li><a href="LogoutPage.php">Sign Out</a></li>
	<li><a href="ShareCalendar.php">Share Calendar</a></li>
	<li><a href="ReminderPage.php">Customise Reminder</a></li>
	<li><a href="AddEventPage.php">Add Event</a></li>	
</ul>

<!-- Content -->	
<div id="timestamp"></div>
<div id="content">
	<div id="calendar"></div>
	
	<div>
		<input id="chart_btn" type="button" onclick="chart()" value="Weekly Stress Level">
		<br>
		<form method="POST" action="#">
			<select name="sleepingHours">
				<optgroup label = "Sleeping Hours">
				<option value="10">10</option>
				<option value="9">9</option>
				<option value="8">8</option>
				<option value="7">7</option>
				<option value="6">6</option>
				<option value="5">5</option>
				<option value="4">4</option>
				<option value="3">3</option>
				<option value="2">2</option>
				<option value="1">1</option>
				<option value="0">0</option>
			</select>
			
			<input type="submit" name="sleepHours">
		</form>			
		
		<div id="piechart" style="width:100%;height:500px;display:block;"></div>
		<div id="radarchart" style="display:none;"><canvas id="graphCanvas"></canvas><div>
	</div>
	
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script>

	google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Category', 'Hours per Day'],
          ['Work',    <?=$durationWork?> ],
          ['Exercise',  <?=$durationExercise?>],
          ['Recreational',  <?=$durationRecretional?>],
          ['Sleep',    <?=$sleepHours?>],
		  ['Free Time',  <?=$durationFreeHours?>]
        ]);

        var options = {
          title: 'My Daily Activities'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
	  
	  
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
	
<?php if($durationBusyHours>8){
		echo "<script> reminder();</script>";
	}?>
</div>

</body>
</html>

