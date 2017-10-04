<?php
	
function UpdateSeason($BotId, $link)
{
	$sql = "SELECT * FROM `seasonids` WHERE `Current` = '1'";
	$result = $link->query($sql);
	if($row = $result->fetch_assoc())
	{
		$CurrentSeason = $row['id'];
		$sql = "SELECT COUNT(*) AS 'Matches' FROM `results` WHERE `SeasonId` = " . $CurrentSeason . " AND (Bot1 = '" . $BotId . "' OR Bot2 = '" . $BotId . "')" ;
		$participantResult  = $link->query($sql);
		if($participantRow = $participantResult->fetch_array(MYSQLI_ASSOC))
		{
			$matches = $participantRow['Matches'];
		}
		else
		{
			$matches = 0;
		}
		$sql = "SELECT COUNT(*) AS 'Wins' FROM `results` WHERE `SeasonId` = " . $CurrentSeason . " AND `Winner` = '" . $BotId . "'";
		$winsResult = $link->query($sql);
		if($winsRow = $winsResult->fetch_array(MYSQLI_ASSOC))
		{
			$wins = $winsRow['Wins'];
		}
		else
		{
			$wins = 0;
		}
		if($matches == 0 || $wins == 0)
		{
			$winpct = 0;
		}
		else
		{
			$winpct = ( $wins / $matches) * 100;
		}
		$sql = "UPDATE `seasons` SET `Matches` = '" . $matches . "', `Wins` = '" . $wins . "', `WinPct` = '" . $winpct . "' WHERE `BotId` = '" . $BotId . "' AND `Season` = '" . $CurrentSeason . "'";
		echo "<br>$sql<br>";
		$link->query($sql);
	}
}
$link = new mysqli("localhost", "root", "" , "sc2ladders");

// Check connection
if($link->connect_error){
	die("ERROR: Could not connect. " . mysqli_connect_error());
}

$sql = "SELECT * FROM `participants`";
$result = $link->query($sql);
while($row = $result->fetch_assoc())
{
	UpdateSeason($row['ID'], $link);
}
$sql = "SELECT * FROM `seasons` ORDER BY `Season` ASC, `WinPct` DESC";
$result = $link->query($sql);
$currentSeason = 0;
$currentPosition = 1;
while($row = $result->fetch_assoc())
{
	if($currentSeason != $row['Season'])
	{
		$currentSeason = $row['Season'];
		$currentPosition = 1;
	}
	$sql = "UPDATE `seasons` SET `Position` = '" . $currentPosition . "' WHERE `id` = '" . $row['id'] . "'";
	$posRes = $link->query($sql);
	$currentPosition ++;
}