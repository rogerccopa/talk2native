<?php
require 'settings.php';

// Create connection
$dbConn = new mysqli($envInfo->db_host, $envInfo->db_un, $envInfo->db_pw, $envInfo->db_name);

if ($dbConn->connect_error) { die("Database connection failed:" . $dbConn->connect_error); }

$userIp = $_SERVER['REMOTE_ADDR'];
$userUid = 0;
$userName = str_replace(' ', '', ucwords($_POST["name"]));

$sql = "INSERT INTO visitors(ip, name, lang1, lang2) 
		VALUES('" . $userIp . "','" . $userName . "'," . $_POST["lang1"] . "," . $_POST["lang2"] . ")";

if ($dbConn->query($sql) === TRUE) {
	$userUid = $dbConn->insert_id;
} else {
	die("Couldn't save form data:" . $dbConn->error);
}

$dbConn->close();
								//$_SERVER['HTTP_HOST']
header("location:" . "http://" . $_SERVER['SERVER_NAME'] . ":2013/user=" . 
						$userName . "_" . $_POST["lang1"] . "_" . $_POST["lang2"] . "-" . $userUid);
?>