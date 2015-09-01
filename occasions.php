<!DOCTYPE html>
<!--
occasions.php
author: Zeust the Unoobian <2noob2banoob@gmail.com>

This page can be used to alter descriptions for occasions (moments)
at which participants can enroll.

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
	try {
		$db = new TechnivalDB("technival", "something", false);
	} catch (Exception $e) {
		echo "<div>Failure</div>";
		$db = null;
	}
	if($_SERVER["REQUEST_METHOD"] == "POST" && $db !== null) {
		try {
			$db->define_occasion($_POST["id"], $_POST["description"]);
			echo "<div>Success</div>";
		} catch (Exception $e) {
			echo "<div>Failure</div>";
		}
	}
	$occasions = null;
	if($db !== null) {
		try {
			$occasions = $db->get_occasions();
		} catch (Exception $e) {
			echo "<div>Failure</div>";
		}
		try { $db->close_con(); } catch (Exception $e) {}
	}
?>

<?php
	if($occasions !== null){
		echo "<table><tr><th>id</th><th>description</th></tr>";
		foreach($occasions as $id => $desc)
			echo "<tr><td>$id</td><td>$desc</td></tr>";
		echo "</table>";
	}
?>

<form action="occasions.php" method="POST">
	<table>
		<tr>
			<td>id:</td>
			<td><input type="number" name="id"/></td>
		</tr>
		<tr>
			<td>Description:</td>
			<td><input type="text" name="description"/></td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit"/></td>
		</tr>
	</table>
</form>

</body>
</html>
