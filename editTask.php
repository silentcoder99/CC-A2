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

    //Process GET parameter
    $task = null;
    if(isset($_GET['id'])) {
        $query = $db->prepare("SELECT * from Tasks WHERE id = ? and owner = ?");
        $query->execute([$_GET['id'], $userEmail]);
        $task = $query->fetch();
    }

    //Process POST requests
    if(isset($_POST['id']) and isset($_POST['name']) and isset($_POST['due_date'])) {
        $query = $db->prepare("UPDATE Tasks SET name = ?, due_date = ? WHERE id = ? and owner = ?");
        $query->execute([$_POST['name'], $_POST['due_date'], $_POST['id'], $userEmail]);

        header("Location: /tasks");
    }

    //Render site header
    include('header.php');

    if($task != null) {
?>

      <main>
      <form  action="editTask" method="post">
        <p>
          <label for="name">Name:</label>
          <input 
            type="text"
            id="name"
            name="name"
            value="<?= $task['name'] ?>"
            required
          />
        </p>

        <p>
          <label for="due_date">Due Date:</label>
          <input 
            type="date"
            id="due_date"
            name="due_date"
            value="<?= $task['due_date'] ?>"
            required
          />
        </p>

        <label></label>

        <input type="hidden" id="id" name="id"
          value="<?= $task['id'] ?>"
        />
        <input type="submit" value="Edit Task" />
      </form>

      <form action="deleteTask" method="post">
        <input type="hidden" id="id" name="id"
          value="<?= $task['id'] ?>"
        />

        <input type="submit" value="Delete Task" />
      </form>
      </main>

<?php 
    }
    else {
?>
      <p>No task with this id exists</p>
<?php
    }
?>

<head>
    <link type="text/css" rel="stylesheet" href="/stylesheets/main.css" />
</head>
