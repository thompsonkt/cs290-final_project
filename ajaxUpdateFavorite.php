<?php
error_reporting(E_All);
ini_set('display_errors', 1);
session_name("MovieDB");
session_start();

$jsonObj = array();
$parameters = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    foreach($_POST as $key=>$val)
    {
        $parameters[$key] = $val;
    }

    if(isset($parameters['movieid']))
    {
        updateFavorite($parameters['movieid'],$_SESSION["username"]);
    }
}

function updateFavorite($movieid, $user)
{
    $mysqli = connectDB();

    $selectStmt = "UPDATE MOVIEDB_USER_MOVIES SET FAVORITE = !FAVORITE WHERE MOVIE_ID = ? AND MOVIE_ADDED_BY_USERNAME = ?";

    if (!($stmt = $mysqli->prepare($selectStmt))) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error . $selectStmt;
    }

    if (!$stmt->bind_param("is", $movieid, $user)) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    /* Execute Statement */
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }

    /* explicit close recommended */
    $stmt->close();

    return $returnVal;
}

function connectDB()
{
    include 'storedInfo.php';
    $mysqli = new mysqli("oniddb.cws.oregonstate.edu","thomkevi-db", $myPassword, "thomkevi-db");
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    } else {
        /* echo "Connection worked!<br>"; */
    }
    return $mysqli;
}

?>
