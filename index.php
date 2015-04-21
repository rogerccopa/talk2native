<?php require("lib.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Talk 2 Native</title>
</head>
<body>
	<div style="text-align: center;"><img src="img/videochat.png" width="300" height="200"/></div>
	
	<table border="0" align="center">
	<tr>
		<td>
		<p>Practice your second language with a native speaker. For free.<br/>And, help others to practice too.</p></td>
	</tr>
	</table>

	<form action="visitor.php" method="post">
	<table align="center" bgcolor="#EEEEEE">
	<tr>
		<td align="right">Enter your name</td>
		<td><input type="text" name="name" autofocus="autofocus"> &nbsp;</td>
	</tr>
	<tr>
		<td align="right">What is your native language?</td>
		<td><select name="lang1">
				<?php
				foreach($langs as $id => $name) {
					echo "<option value=" . $id . ">" . $name . "</option>";		
				} ?>
			</select></td>
	</tr>
	<tr>
		<td align="right">What language do you want to practice?</td>
		<td><select name="lang2">
				<?php
				foreach($langs as $id => $name) {
					echo "<option value=" . $id . ">" . $name . "</option>";		
				} ?>
			</select></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value=" Enter "></td>
	</tr>
	</table>
	</form>
	<div align="center" style="color:gray;font-family:Arial;font-size:8;"><b>Note:</b> We support only Chrome and Firefox.</div>
	<br/>
	<div style="text-align:center;">Sample Users</div>
	<table align="center" width="60%">
	<tr bgcolor="#FFFFCC">
		<th>Name</th>
		<th>Native Language</th>
		<th>Wants To Practice</th>
		<th>Status</th>
	</tr>
	<tr>
		<td>Bob</td>
		<td>English</td>
		<td>Spanish</td>
		<td>Talking (busy)</td>
	</tr>
	<tr bgcolor="#EEEEEE">
		<td>Joe</td>
		<td>English</td>
		<td>French</td>
		<td>&nbsp;<input type="button" value="Start video chat with Joe"></td>
	</tr>
	<tr>
		<td>Michael</td>
		<td>French</td>
		<td>German</td>
		<td>Available</td>
	</tr>
	<tr bgcolor="#EEEEEE">
		<td>John</td>
		<td>Japanese</td>
		<td>Spanish</td>
		<td>&nbsp;<input type="button" value="Start video chat with John"></td>
	</tr>
	</table>
</body>
</html>