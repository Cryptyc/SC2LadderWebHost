<?php
session_start();

require_once("header.php");

	header('Content-Type: text/html; charset=utf-8');


	if(!isset($_REQUEST['id']))
	{
		die("no bot");
	}

	$sql = "SELECT * FROM `participants` WHERE `ID` = '" . mysqli_real_escape_string($link, $_REQUEST['id']) . "'";
	$result = $link->query($sql);
	if(!$row = $result->fetch_assoc())
	{
		die("unable to get bot");
	}
	$Botrace = GetRace($row['Race']);
	echo "<h3>Showing results for " . $row['Name'] . " Playing as " . $Botrace . "</h3>";

	$sql = "SELECT `participant1`.`ID` AS P1ID,
		`participant1`.`Name` AS P1Name,
 		`participant1`.`Race` AS P1Race,
		`participant2`.`ID` AS P2ID,
		`participant2`.`Name` AS P2Name,
		`participant2`.`Race` AS P2Race,
		`results`.`Winner` AS Winner,
		`results`.`Result` AS Result,
		`results`.`Bot1Change` AS Bot1Change,
		`results`.`Bot2Change` AS Bot2Change,
		`results`.`Map` AS Map,
		`results`.`ReplayFile` AS ReplayFile,
		`results`.`Crash` AS Crash,
		`results`.`Date` AS MatchDate,
		`results`.`Bot1AvgFrame` AS Bot1AvgFrame,
		`results`.`Bot2AvgFrame` AS Bot2AvgFrame,
		`results`.`Frames` AS Frames
	FROM `participants` AS `participant1`,
		`participants` AS `participant2`,
		`results`
	WHERE
	SeasonId ='" . mysqli_real_escape_string($link, $_REQUEST['season']) . "'
	AND (`results`.Bot1='" . mysqli_real_escape_string($link, $_REQUEST['id']) . "' OR `results`.`Bot2`='" . mysqli_real_escape_string($link, $_REQUEST['id']) . "')
	AND `results`.`Bot1`= `participant1`.`ID`
	AND `results`.`Bot2` = `participant2`.`ID`
	ORDER BY `MatchDate` DESC";

	$result = $link->query($sql);
	if($result->num_rows < 1)
	{
		echo "<p>No Results available</p>";
		die();
	}
?>
			<table id="MatchHistoryTable" class="table table-striped" style="width: auto;">
	<tr>
		<th onclick="bubbleSortTable('MatchHistoryTable', 0)">Time</th>
		<th onclick="bubbleSortTable('MatchHistoryTable', 1)">Opponent Name</th>
		<th onclick="bubbleSortTable('MatchHistoryTable', 2)">Race</th>
		<th onclick="bubbleSortTable('MatchHistoryTable', 3)">Map</th>
		<th onclick="bubbleSortTable('MatchHistoryTable', 4)">Result</th>
<?php
if($_REQUEST['season'] > 4)
{
		echo "<th onclick=\"bubbleSortTable('MatchHistoryTable', 5)\">ELO Change</th>";
}
?>
		<th onclick="bubbleSortTable('MatchHistoryTable', 6)">Replay</th>
<?php
if($_REQUEST['season'] > 5)
{
		echo "<th onclick=\"bubbleSortTable('MatchHistoryTable', 7)\">Game Time</th>";
		echo "<th onclick=\"bubbleSortTable('MatchHistoryTable', 7)\">Avg Frame</th>";
}
	echo "</tr>";


	while($row = $result->fetch_assoc())
	{
		echo "<tr>";
		echo "<td>";
		if($row['MatchDate'] == "")
		{
			echo "Unavailable";
		}
		else
		{
			$phpdate = strtotime( $row['MatchDate'] );
			$phpdate = date( 'Y-m-d H:i:s', $phpdate );
			echo $phpdate;
		}
		echo "</td>";
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
		else if ($row['Winner'] < 1)
		{
			echo $row['Result'];
		}
		else if($row['Result'] == "Player1Crash" || $row['Result'] == "Player2Crash")
		{
			echo "Crash";
		}
		else if($row['Crash'] == $_REQUEST['id'])
		{
			echo "Crash";
		}
		else
		{
			echo "Loss";
		}
		echo "</td>";
		if($_REQUEST['season'] > 4)
		{
			echo "<td>";
			if($row['P1ID'] == $_REQUEST['id'])
			{
				echo $row['Bot1Change'];
			}
			else 
			{
				echo $row['Bot2Change'];
			}
			echo "</td>";
		}
		echo "<td>";
		if($row['ReplayFile'] == "")
		{
			echo "Unavailable";
		}
		else
		{
			echo "<button type=\"button\" id=\"Replay\" class=\"btn btn-info navbar-btn\" onclick=\"window.location.href='" . $row['ReplayFile'] . "'\">
                                <span>Replay</span>
                            </button>";

//			echo "<a href=\"" . $row['ReplayFile'] . "\">Replay</a>";
		}
		echo "</td>
			<td>";
		if($row["Frames"] > 0)
		{
			$seconds = $row["Frames"] / 22.4;
			echo gmdate("H:i:s", $seconds);
		}
		echo "</td>
			<td>";
		if($row['P1ID'] == $_REQUEST['id'] && $row["Bot1AvgFrame"] > 0)
		{
			echo $row["Bot1AvgFrame"];
		}
		else if ($row['P2ID'] == $_REQUEST['id'] && $row["Bot2AvgFrame"] > 0)
		{
			echo $row["Bot2AvgFrame"];
		}
		
		echo "</td></tr>";
	}
	echo "</table>";
	require_once("footer.php");
	?>
	</body>
	</html>
