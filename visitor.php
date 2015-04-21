<?php
require 'settings.php';

// Create connection
$dbConn = new mysqli($envInfo->db_host, $envInfo->db_un, $envInfo->db_pw, $envInfo->db_name);

if ($dbConn->connect_error) { die("Database connection failed:" . $dbConn->connect_error); }

$userIp = $_SERVER['REMOTE_ADDR'];
$userUid = 0;
$sql = "INSERT INTO visitors(ip, name, lang1, lang2) 
		VALUES('" . $userIp . "','" . $_POST["name"] . "'," . $_POST["lang1"] . "," . $_POST["lang2"] . ")";

if ($dbConn->query($sql) === TRUE) {
	$userUid = $dbConn->insert_id;
} else {
	die("Couldn't save form data:" . $dbConn->error);
}

$dbConn->close();
								//$_SERVER['HTTP_HOST']
header("location:" . "http://" . $_SERVER['SERVER_NAME'] . ":2013/user=" . $_POST["name"] . "_" . $_POST["lang1"] . "_" . $_POST["lang2"] . "-" . $userUid);
//header("location:" . "http://192.168.43.249:2013/user=" . $_POST["name"] . "-" . $userUid . "_" . $_POST["lang1"] . "_" . $_POST["lang2"]);
//header("location:" . "visitors.php?l1=" . $_POST["lang1"] . "&l2=" . $_POST["lang2"] . "&uid=" . $userUid)
?>