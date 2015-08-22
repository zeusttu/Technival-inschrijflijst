<!DOCTYPE html>
<!--
index.php
author: Zeust the Unoobian <2noob2banoob@gmail.com>

This page can be used to enter participants.
-->

<?php include("technivaldb.php") ?>

<html>
<head>
	<title>Technival inschrijflijst</title>
</head>
<body>

<?php
	if($_SERVER["REQUEST_METHOD"] == "POST") {
		try {
			$db = new TechnivalDB("technival.db", "something", false);
			$occ = null;
			if(isset($_POST["occasion"])) $occ = $_POST["occasion"];
			$db->new_participant($_POST["name"], $occ);
			$db->close_con();
			echo "<div>Success</div>";
		} catch (Exception $e) {
			echo "<div>Failure</div>";
		}
	}
?>

<form action="index.php" method="POST">
	<table>
		<tr>
			<td>Name:</td>
			<td><input type="text" name="name"/></td>
		</tr>
		<tr>
			<td>Occasion:</td>
			<td><input type="number" name="occasion" value=1/></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit"/></td>
		</tr>
	</table>
</form>

</body>
</html>
