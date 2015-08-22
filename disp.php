<!DOCTYPE html>
<!--
disp.php
author: Zeust the Unoobian <2noob2banoob@gmail.com>

This page will display all participants.
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
			$db = new TechnivalDB("technival.db", "something");
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
