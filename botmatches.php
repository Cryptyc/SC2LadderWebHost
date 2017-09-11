<?php

	function GetRace($RaceId)
	{
		switch($RaceId)
		{
			case 0:
				return "Terran";
			case 1:
				return "Zerg";
			case 2:
				return "Protoss";
			default:
				die("Unknown race" . $RaceId);
		}
	}
	
	header('Content-Type: text/html; charset=utf-8');

	echo "<html>";
	if(!isset($_REQUEST['id']))
	{
		die("no bot");
	}
	$link = new mysqli("localhost", "root", "", "sc2ladders");
 
	// Check connection
	if($link->connect_error){
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}
	$sql = "SELECT * FROM `participants` WHERE `ID` = '" . mysqli_real_escape_string($link, $_REQUEST['id']) . "'";
	$result = $link->query($sql);
	if(!$row = $result->fetch_assoc())
	{
		die("unable to get bot");
	}
	$Botrace = GetRace($row['Race']);
	echo "<p>Showing results for " . $row['Name'] . " Playing as " . $Botrace . "</p>";
	
	$sql = "SELECT `participant1`.`ID` AS P1ID, 
		`participant1`.`Name` AS P1Name,
 		`participant1`.`Race` AS P1Race, 
		`participant2`.`ID` AS P2ID, 
		`participant2`.`Name` AS P2Name, 
		`participant2`.`Race` AS P2Race,
		`results`.`Winner` AS Winner,
		`results`.`Map` AS Map,
		`results`.`ReplayFile` AS ReplayFile
	FROM `participants` AS `participant1`, 
		`participants` AS `participant2`, 
		`results` 
	WHERE
	(`results`.Bot1='" . mysqli_real_escape_string($link, $_REQUEST['id']) . "' OR `results`.`Bot2`='" . mysqli_real_escape_string($link, $_REQUEST['id']) . "')
	AND `results`.`Bot1`= `participant1`.`ID`
	AND `results`.`Bot2` = `participant2`.`ID`";
	
	
	$result = $link->query($sql);
	if($result->num_rows < 1)
	{
		echo "<p>No Results available</p>";
		die();
	}

	echo "<table>
	<tr>
		<th>Opponent Name</th>
		<th>Race</th>
		<th>Map</th>
		<th>Result</th>
		<th>Replay</th>
	</tr>";
	
	while($row = $result->fetch_assoc())
	{
		echo "<tr>";
		if($row['P1ID'] == $_REQUEST['id'])
		{
			echo "<td>" . $row['P2Name'] . "</td>
			<td>" . GetRace($row['P2Race']) . "</td>";
		}
		else
		{
			echo "<td>" . $row['P1Name'] . "</td>
			<td>" . GetRace($row['P1Race']) . "</td>";
		}
		echo "<td>" . $row['Map'] . "</td>";
		echo "<td>";
		if($row['Winner'] == $_REQUEST['id'])
		{
			echo "Win";
		}
		else
		{
			echo "Loss";
		}
		echo "</td>";
		echo "<td><a href=\"" . $row['ReplayFile'] . "\">Replay</a></td>";
		echo "</tr>";
	}
	echo "</table></html>";
