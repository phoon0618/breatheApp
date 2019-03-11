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

    // DATA FOR CHART
    // header('Content-Type: application/json');

    $data = array();
    foreach ($result as $row) {
        $data[] = $row;
    }

    echo json_encode($data);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Your Rush Level Today</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <style type="text/css">
    BODY {
        width: 550PX;
    }

    #chart-container {
        width: 100%;
        height: auto;
    }
    </style>

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.bundle.min.js'>
    </script>
</head>

<body>
    <ul class="nav justify-content-end">
        <!-- <li class="nav-item">
            <a id="addButton" class="nav-link" href="AddEvent.php">Add Event</a>
        </li> -->
        <li class="nav-item">
            <a id="dashboardButton" class="nav-link" href="DashboardPage.php">My Calendar</a>
        </li>
        <li class="nav-item">
            <a id="logoutButton" class="nav-link btn btn-danger" href="LogoutPage.php">Sign Out</a>
        </li>
    </ul>

    <div class="container">
        <div id="chart-container">
            <canvas id="graphCanvas"></canvas>
        </div>
    </div>
    <script>
    $(document).ready(function() {
        showGraph();
    });


    function showGraph() {
        {
            $.post("ChartPage.php",
                function(data) {
                    console.log(data);
                    var name = [];
                    var marks = [];

                    for (var i in data) {
                        name.push(data[i].student_name);
                        marks.push(data[i].marks);
                    }

                    var chartdata = {
                        labels: name,
                        datasets: [{
                            label: 'Rush Level',
                            backgroundColor: '#49e2ff',
                            borderColor: '#46d5f1',
                            hoverBackgroundColor: '#CCCCCC',
                            hoverBorderColor: '#666666',
                            data: marks
                        }]
                    };

                    var graphTarget = $("#graphCanvas");

                    var barGraph = new Chart(graphTarget, {
                        type: 'bar',
                        data: chartdata
                    });
                });
        }
    }
    </script>


</body>

</html>