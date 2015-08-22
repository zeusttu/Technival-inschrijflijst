<!DOCTYPE html>
<!--
disp.php
author: Zeust the Unoobian <2noob2banoob@gmail.com>

This page will display all participants.

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

<table>
	<tr>
		<th>Occasion</th>
		<th>Name</th>
	</tr>
	<?php
		try{
			$db = new TechnivalDB("technival", "something");
			$stuff = $db->get_participants();
			$db->close_con();
			for($i=0; $i<count($stuff); $i++) echo "<tr><td>".$stuff[$i][1]."</td><td>".$stuff[$i][0]."</td></tr>";
		} catch(Exception $e) {
			echo "<tr><td colspan=\"2\" style=\"color: red; font-weight: bold;\">Error</td></tr>";
			echo "<tr><td colspan=\"2\" style=\"color: red; font-weight: bold;\">$e</td></tr>";
		}
	?>
</table>

</body>
</html>
