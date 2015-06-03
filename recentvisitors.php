<?php
	require 'settings.php';
	require 'lib.php';

	// Create connection
	$dbConn = new mysqli($envInfo->db_host, $envInfo->db_un, $envInfo->db_pw, $envInfo->db_name);

	if ($dbConn->connect_error) { die("Database connection failed:" . $dbConn->connect_error); }

	$sql = "SELECT uid, dtime, ip, name, lang1, lang2 FROM visitors WHERE name <> 'testing' ORDER BY uid DESC LIMIT 100";
	$visitors = $dbConn->query($sql);
	$dbConn->close();

	if ($visitors->num_rows == 0) {	die("No records found on Visitors table"); }

	echo "LAST 100 VISITORS<br/>";

	while($visitor = $visitors->fetch_assoc()) {
        echo $visitor["dtime"] . " - " . $visitor["name"] . " - " . $langs[$visitor["lang1"]] . " - " . $langs[$visitor["lang2"]] . "<br/>";
    }
?>