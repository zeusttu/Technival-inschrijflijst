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
<script type="text/javascript">
	function update_select() {
		var val = document.getElementById("occasion").value;
		sel = document.getElementById("occasion_desc");
		opt = sel.options;
		for(i=0; i < opt.length-1 && opt[i].value < val; i++);
		if(opt[i].value == val) sel.selectedIndex = i;
		else sel.selectedIndex = 0; 
	}
	function update_num() {
		document.getElementById("occasion").value = document.getElementById("occasion_desc").value;
	}
</script>
<body>

<?php
	try {
		$db = new TechnivalDB("technival", "something", false);
	} catch (Exception $e) {
		echo "<div>Failure: $e</div>";
		$db = null;
	}
	if($_SERVER["REQUEST_METHOD"] == "POST" && $db !== null) {
		try {
			$occ = null;
			if(isset($_POST["occasion"])) $occ = $_POST["occasion"];
			$db->new_participant($_POST["name"], $occ);
			echo "<div>Success</div>";
		} catch (Exception $e) {
			echo "<div>Failure: $e</div>";
		}
	}
	$occasions = null;
	if($db !== null) {
		try {
			$occasions = $db->get_occasions();
		} catch (Exception $e) {
			echo "<div>Failure: $e</div>";
		}
		try { $db->close_con(); } catch (Exception $e) {}
	}
?>

<form action="index.php" method="POST">
	<table>
		<tr>
			<td>Name:</td>
			<td colspan="2"><input type="text" name="name"/></td>
		</tr>
		<tr>
			<td>Occasion:</td>
			<td>
				<input type="number" name="occasion" id="occasion" onchange="javascript:update_select()" defaultvalue=1/>
			</td>
			<td>
				<select name="occasion_desc" id="occasion_desc" onchange="javascript:update_num()" value="1">
					<option></option>
					<?php
						if ($occasions !== null)
							foreach($occasions as $id => $desc)
								echo "<option value=\"$id\">$desc</option>";
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="3"><input type="submit"/></td>
		</tr>
	</table>
</form>

</body>
</html>
