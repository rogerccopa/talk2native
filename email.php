<?php
if($_POST){
	$arrSuggestion = explode("\t",$_POST['message']);	// message: suggestion \t userId
    $fromEmail = "visitor@talk2native.com";
    $headers = "From: " . $fromEmail;

    require 'settings.php';

	// Create connection
	$dbConn = new mysqli($envInfo->db_host, $envInfo->db_un, $envInfo->db_pw, $envInfo->db_name);

	if ($dbConn->connect_error) { die("Database connection failed:" . $dbConn->connect_error); }

	$userIp = $_SERVER['REMOTE_ADDR'];
	$sql = "INSERT INTO messages(ip, message) 
			VALUES('" . $arrSuggestion[1] . "','" . str_replace("'","''",$arrSuggestion[0]) . "')";

	if ($dbConn->query($sql) === TRUE) {
		// good
	} else {
		die("Couldn't save form data:" . $dbConn->error);
	}

	$dbConn->close();

    mail("rogerccopa@gmail.com", $arrSuggestion[0], $headers);

    echo "Sent";
}
?>