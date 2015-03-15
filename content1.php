<?php
error_reporting(E_All);
ini_set('display_errors', 1);
session_start();

if (isset($_GET['logout'])) {
    $_SESSION = array();
    session_destroy();
    $filePath = explode('/', $_SERVER['PHP_SELF'], -1);
    $filePath = implode('/', $filePath);
    $redirect = "http://" . $_SERVER['HTTP_HOST'] . $filePath;
    header("Location: {$redirect}/login.php", true);
    /*  http_redirect("login.php"); */
    die();
}

echo '<!DOCTYPE html>
            <html>
            <head>
            <meta charset="UTF-8">
            <title>content1</title>
            </head>
            <body>
            <div>';

function displayWelcome() {
    echo "Hello " . $_SESSION['username'];
    echo " you have visited this page ";
    echo $_SESSION['visit'];
    echo " times before. Click ";
    echo '<a href="content1.php?logout=true">here</a> ';
    echo "to logout.<br><br>";
    echo 'Click <a href="content2.php">here</a> to go to the content2.php page.';
}

if(isset($_SESSION['username']))
{
    $_SESSION['visit']++;
    displayWelcome();
}
elseif (isset($_POST['username']) && $_POST['username'] != "" && !is_null($_POST['username']))
{
    $_SESSION['visit'] = 0;
    $_SESSION['username'] = $_POST['username'];
    displayWelcome();
}
else
{
    echo "A username must be entered. Click ";
    echo '<a href="login.php">here</a>';
    echo " to return to the login screen.";
}



?>

</div>
</body>
</html>