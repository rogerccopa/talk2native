<?php
require("settings.php");
require("lib.php");

$dbConn = new mysqli($envInfo->db_host, $envInfo->db_un, $envInfo->db_pw, $envInfo->db_name);
if ($dbConn->connect_error) { die("Database connection failed:" . $dbConn->connect_error); }

$userUid = $_GET["uid"];
$sql = "SELECT lang1, lang2 FROM visitors WHERE uid = " . $userUid . ";";
if (!$result = $dbConn->query($sql)) { die("Couldn't get user info: " . $dbConn->error); }

$user = $result->fetch_array();
$sql = "SELECT uid, name, lang1, lang2, status 
		FROM visitors 
		WHERE active = 1 and (lang1 = " . $user["lang2"] . " and lang2 = " . $user["lang1"] . ")";
if (!$result = $dbConn->query($sql)) { die("Couldn't get users" . $dbConn->error); }
$dbConn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Talk 2 Native - User list</title>
</head>
<body>
<div style="text-align: center;">Users connected right now who speaks your selected languages:</div>
<br/>
<div style="text-align: center;font-weight:bold;">Active Users</div>
<table align="center" width="60%">
<tr bgcolor="#CCCCCC">
		<th>User</th>
		<th>Native Language</th>
		<th>Wants To Practice</th>
		<th>Status</th>
	</tr>
<?php
$n = 0;
while ($user = $result->fetch_array()) {
	if ($userUid == $user["uid"]) {
		echo "<tr bgcolor=\"#FFFFCC\">";
	} else {
		echo ($n%2 == 1 ? "<tr bgcolor=\"#EEEEEE\">" : "<tr>");
	}

	echo "<td>" . $user["name"] . "</td>";
	echo "<td>" . $langs[$user["lang1"]] . "</td>";
	echo "<td>" . $langs[$user["lang2"]] . "</td>";
	echo "<td>" . 
			($userUid == $user["uid"] ? "Ready to get invited" : 
			($user["status"] == 2 ? "Speaking" : 
				'<input type="button" value="Invite to Speak" id="btn' . $user["uid"] . '" class="btnStartVid">')) . "</td>";
	
	echo "</tr>";
	$n++;
}
?>
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script>
$(document).ready(function(){
	$(".btnStartVid").click(function(){
		var callerId = window.location.search.split('&')[2].substring(4);
		var calleeId = this.id.substring(3);
		$.ajax({
			url : "updateuserstatus.php",
			type : "POST",
			data : {callerUid:callerId, calleeUid:calleeId},
			success : function(data, textStatus, jqXHR){
				var response = JSON.parse(data);
				
				window.location.href = 	"http://<?php echo $_SERVER['SERVER_NAME'];  ?>:2013" + 
										"/"+response.callerName + "." + callerId + "+" + response.calleeName + "." + calleeId;
			},
			error : function(jqXHR, textStatus, errorThrown){
				alert("Sorry, could not start video chat at this time.\n\n" + errorThrown);
			}
		});
	});
});
</script>
</table>
</body>
</html>
