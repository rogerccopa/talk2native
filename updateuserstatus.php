<?php
require("settings.php");
require("lib.php");

$dbConn = new mysqli($envInfo->db_host, $envInfo->db_un, $envInfo->db_pw, $envInfo->db_name);
if ($dbConn->connect_error) { die("ERROR: Database connection failed. " . $dbConn->connect_error); }

$callerUid = $_POST["callerUid"];
$calleeUid = $_POST["calleeUid"];

$sql = "UPDATE visitors SET " .
		"status = 2, " .
		"callerUid = " . $callerUid . ", " .
		"updated = NOW() " .
		"WHERE uid = " . $calleeUid . " and status = 1";
if (!$result = $dbConn->query($sql)) { die("ERROR: Couldn't update callee user. " . $dbConn->error); }

if ($dbConn->affected_rows == 0) { echo '{"error":"Could not update callee user"}'; exit(); }

$sql = "UPDATE visitors SET " .
		"status = 2, " .
		"updated = NOW() " .
		"WHERE uid = " . $callerUid;
if (!$result = $dbConn->query($sql)) { die("ERROR: Couldn't update users status. " . $dbConn->error); }

$sql = "SELECT uid, name, lang1, lang2 
		FROM visitors 
		WHERE uid IN (" . $callerUid . "," . $calleeUid . ")";
if (!$result = $dbConn->query($sql)) { die("ERROR: Couldn't get users. " . $dbConn->error); }
$dbConn->close();

$rowCaller = null;
$rowCallee = null;
while ($rowUser = $result->fetch_array()) {
	if ($rowUser["uid"] == $callerUid) 	{ $rowCaller = $rowUser;	}
	else 								{ $rowCallee = $rowUser;	}
}

echo '{"callerName":"' . $rowCaller["name"] . '", "calleeName":"' . $rowCallee["name"] . 
	 '", "chatRoomId":"' . $rowCaller["uid"] . '_' . $rowCallee["uid"] . '"}';
?> 
