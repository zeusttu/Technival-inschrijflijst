<!DOCTYPE html>
<!--
index.php
author: Zeust the Unoobian <2noob2banoob@gmail.com>

This page can be used to enter participants.

This file is part of Technival-inschrijflijst.

Technival-inschrijflijst is free software: you can redistribute it
and/or modify it under the terms of the GNU General Public License
as published by the Free Software Foundation, either version 3 of
the License, or (at your option) any later version.

DigitalLockin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with DigitalLockin. If not, see <http://www.gnu.org/licenses/>.
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
			$db = new TechnivalDB("technival", "something", false);
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
