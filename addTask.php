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

    //Process POST request
    if(isset($_POST['name']) and isset($_POST['due_date'])) {
        $query = $db->prepare("INSERT INTO Tasks(name, owner, due_date) VALUES(?, ?, ?)");
        $query->execute([$_POST['name'], $userEmail, $_POST['due_date']]);

        header("Location: /tasks");
    }

    //Render site header
    include('header.php');
?>

  <main>
  <form  action="addTask" method="post">
    <p>
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" placeholder="Enter task name" required />
    </p>

    <p>
      <label for="due_date">Due Date:</label>
      <input type="date" id="due_date" name="due_date" placeholder="Enter due date" required />
    </p>

    <label></label>
    <input type="submit" value="Add task" />
  </form>
  </main>

<head>
    <link type="text/css" rel="stylesheet" href="/stylesheets/main.css" />
</head>
