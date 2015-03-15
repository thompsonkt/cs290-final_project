<?php
error_reporting(E_All);
ini_set('display_errors', 1);
session_name("MovieDB");
session_start();

function returnLoginOrAccount()
{
    if(isset($_SESSION['username']))
    {
        echo "<a href=account.php>" . $_SESSION["username"] . " Account</a>";
    }
    else {
        echo "<a href=login.php>Login</a>";
    }

}

function showLogOut()
{
    if(isset($_SESSION['username']))
    {
        echo '<a href="main.php?logout=true">Logout</a>';
    }
}

if (isset($_GET['logout'])) {
    $_SESSION = array();
    session_destroy();
    $filePath = explode('/', $_SERVER['PHP_SELF'], -1);
    $filePath = implode('/', $filePath);
    $redirect = "http://" . $_SERVER['HTTP_HOST'] . $filePath;
    header("Location: {$redirect}/main.php", true);
    /*  http_redirect("login.php"); */
    die();
}

?>