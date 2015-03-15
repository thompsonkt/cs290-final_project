<?php
error_reporting(E_All);
ini_set('display_errors', 1);
session_name("MovieDB");
session_start();

$jsonObj = array();
$parameters = array();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    foreach($_GET as $key=>$val)
    {
        $parameters[$key] = $val;
    }
    $jsonObj["Type"] = "[GET]";

}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    foreach($_POST as $key=>$val)
    {
        $parameters[$key] = $val;
    }
    $jsonObj["Type"] = "[POST]";

    if(isset($parameters['user']) && isset($parameters['pass']) && validateAccount($parameters['user'], $parameters['pass']) === 1)
    {
        $parameters['Account'] = 'Valid';
        $_SESSION['username'] = $parameters['user'];
    }
    else
    {
        $parameters['Account'] = 'InValid';
    }
}

if (count($parameters) == 0)
{
    $jsonObj["parameters"] = null;
} else {
    $jsonObj["parameters"] = $parameters;
}

echo json_encode($jsonObj);

function validateAccount($user, $pass)
{
    $returnVal = 0;


    $mysqli = connectDB();

    $selectStmt = "SELECT username FROM MOVIEDB_USERS WHERE USERNAME = ? AND PASSWORD = ?";

    if (!($stmt = $mysqli->prepare($selectStmt))) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error . $selectStmt;
    }

    if (!$stmt->bind_param("ss", $user, $pass)) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    /* Execute Statement */
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }

    $out_username = NULL;
    if (!$stmt->bind_result($out_username)) {
        echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    while ($stmt->fetch()) {
        $returnVal = 1;
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
