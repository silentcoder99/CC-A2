<?php
    //Get user's email
    $userId = $_SERVER['HTTP_X_GOOG_AUTHENTICATED_USER_EMAIL'];
    if(!$userId) {
        $userId = 'testing.accounts:Test User';
    }
    $userEmail = explode(':', $userId)[1];

    //Connect to database
    $dsn = getenv('MYSQL_DSN');
    if(strpos(getenv('SERVER_SOFTWARE'), 'Development') === 0) {
        $dsn = 'mysql:host=localhost;port=3306;dbname=todos';
    }
    $user = getenv('MYSQL_USER');
    $password = getenv('MYSQL_PASSWORD');
    $db = new PDO($dsn, $user, $password);

    //Get user's tasks
    $query = $db->prepare("SELECT * from Tasks WHERE owner = ?");
    $query->execute([$userEmail]);
    $tasks = $query->fetchAll();


    //Calculate graph values

    $dateTally = array();
    $dataPoints = array();

    foreach($tasks as $task) {
        if(!isset($dateTally[$task['due_date']])) {
            $dateTally[$task['due_date']] = 1;
        }
        else {
            $dateTally[$task['due_date']] += 1;
        }

    }

    foreach($dateTally as $date => $tally) {
        array_push(
            $dataPoints,
            array("label"=> $date, "y"=> $tally)
        );
    }

    //$dataPoints = array(
        //array("x"=> 10, "y"=> 41),
        //array("x"=> 20, "y"=> 35),
        //array("x"=> 30, "y"=> 50),
        //array("x"=> 40, "y"=> 45),
        //array("x"=> 50, "y"=> 52),
        //array("x"=> 60, "y"=> 68),
        //array("x"=> 70, "y"=> 38),
        //array("x"=> 80, "y"=> 71),
        //array("x"=> 90, "y"=> 52),
        //array("x"=> 100, "y"=> 60),
        //array("x"=> 110, "y"=> 36),
        //array("x"=> 120, "y"=> 49),
        //array("x"=> 130, "y"=> 41)
    //);

    //Render site header
    include('header.php');
?>

<html>
<head>  
<script>
window.onload = function () {
 
var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	exportEnabled: true,
	theme: "light1",
	title:{
		text: "Amount of Tasks Due"
	},
	data: [{
		type: "column",
		indexLabelFontColor: "#5A5757",
		indexLabelPlacement: "outside",   
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();
 
}
</script>
</head>

<body>
<main>
<h1>Todos</h1>
<ul>
    <?php
        foreach($tasks as $task) {
            echo '<li>' .
                '<a href="editTask?id=' .
                    $task['id'] .
                '" />' .
                    $task['name'] .
                '</a><br/>' .
                '<span>Due: ' .
                    $task['due_date'] .
                '</span></li>';
        }
    ?>
</ul>

<a href="addTask">Add Task</a>

<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</main>
</body>

<head>
    <link type="text/css" rel="stylesheet" href="/stylesheets/main.css" />
</head>
</html>
