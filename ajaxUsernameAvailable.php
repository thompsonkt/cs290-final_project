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

    if(isset($parameters['user']) && validateAccount($parameters['user']) === 1)
    {
        $parameters['Account'] = 'Not Available';
    }
    else
    {
        $parameters['Account'] = 'Available';
        createAccount($parameters['user'], $parameters['pass'], $parameters['fname'], $parameters['lname'], $parameters['email']);
        $_SESSION['username'] = $parameters['user'];
    }
}

if (count($parameters) == 0)
{
    $jsonObj["parameters"] = null;
} else {
    $jsonObj["parameters"] = $parameters;
}

echo json_encode($jsonObj);

function validateAccount($user)
{
    $returnVal = 0;


    $mysqli = connectDB();

    $selectStmt = "SELECT username FROM MOVIEDB_USERS WHERE USERNAME = ?";

    if (!($stmt = $mysqli->prepare($selectStmt))) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error . $selectStmt;
    }

    if (!$stmt->bind_param("s", $user)) {
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

function createAccount($user, $pass, $fname, $lname, $email)
{
    $mysqli = connectDB();
    /* Prepared statement, stage 1: prepare */
    if (!($stmt = $mysqli->prepare("INSERT INTO MOVIEDB_USERS (username, password, firstname, lastname, email) VALUES (?, ?, ?, ?, ?)"))) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }

    /* Prepared statement, stage 2: bind and execute */
    if (!$stmt->bind_param("sssss", $user, $pass, $fname, $lname, $email)) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    /* Execute Statement */
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    /* explicit close recommended */
    $stmt->close();
}

?>
