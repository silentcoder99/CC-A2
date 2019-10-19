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

    //Process POST requests
    if(isset($_POST['id'])) {
        $query = $db->prepare("DELETE FROM Tasks WHERE id = ? and owner = ?");
        $query->execute([$_POST['id'], $userEmail]);
    }
    header("Location: /tasks");
?>
