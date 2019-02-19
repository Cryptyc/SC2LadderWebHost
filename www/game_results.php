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
			case 3:
				return "Random";
			default:
				die("Unknown race" . $RaceId);
		}
	}
	$races[0] = "Terran";
	$races[1] = "Zerg";
	$races[2] = "Protoss";
	$races[3] = "Random";
	require_once("dbconf.php");
	$link = new mysqli($host, $username, $password , $db_name);

	// Check connection
	if($link->connect_error){
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}
	
	$sql = "SELECT `participant1`.`ID` AS P1ID,
		`participant1`.`Name` AS P1Name,
 		`participant1`.`Race` AS P1Race,
		`participant1`.`CurrentELO` AS `P1ELO`,
		`author1`.`username` AS P1Author,
		`participant2`.`ID` AS P2ID,
		`participant2`.`Name` AS P2Name,
		`participant2`.`Race` AS P2Race,
		`participant2`.`CurrentELO` AS `P2ELO`,
		`author2`.`username` AS P2Author,
		`results`.`GameId` AS GameId,
		`results`.`Winner` AS Winner,
		`results`.`Result` AS Result,
		`results`.`Bot1Change` AS Bot1Change,
		`results`.`Bot2Change` AS Bot2Change,
		`results`.`Map` AS Map,
		`results`.`ReplayFile` AS ReplayFile,
		`results`.`Crash` AS Crash,
		`results`.`Date` AS MatchDate,
		`results`.`Frames` AS Frames
		FROM `participants` AS `participant1`,
		`members` AS `author1`,
		`participants` AS `participant2`,
		`members` AS `author2`,
		`results`
		WHERE `results`.`seasonid`='6'
		AND `results`.`Bot1`= `participant1`.`ID`
		AND `participant1`.`Author` = `author1`.`id`
		AND `results`.`Bot2` = `participant2`.`ID`
		AND `participant2`.`Author` = `author2`.`id`
		ORDER BY `MatchDate` DESC LIMIT 150";
	$result = $link->query($sql);
	$i = 0;
	while($row = $result->fetch_assoc())
	{
		$jsonResults[$i]["id"] = $row["GameId"];
		$jsonResults[$i]["map"] = $row["Map"];
		if($row["Winner"] == $row["P1ID"])
		{
			$jsonResults[$i]["winner_name"] = $row["P1Name"];
		}
		else if ($row["Winner"] == $row["P2ID"])
		{
			$jsonResults[$i]["winner_name"] = $row["P2Name"];
		}
		else
		{
			$jsonResults[$i]["winner_name"] = "null";
		}
		$jsonResults[$i]["replay"] = $row["ReplayFile"];
		$seconds = $row["Frames"] / 22.4;
		$jsonResults[$i]["matchtime"] = gmdate("H:i:s", $seconds);
		$jsonResults[$i]["bots"][0]["author"] = $row["P1Author"];
		$jsonResults[$i]["bots"][0]["name"] = $row["P1Name"];
		$jsonResults[$i]["bots"][0]["race"] = $races[$row["P1Race"]];
		$jsonResults[$i]["bots"][0]["elo"] = $row["P1ELO"];
		$jsonResults[$i]["bots"][1]["author"] = $row["P2Author"];
		$jsonResults[$i]["bots"][1]["name"] = $row["P2Name"];
		$jsonResults[$i]["bots"][1]["race"] = $races[$row["P1Race"]];
		$jsonResults[$i]["bots"][1]["elo"] = $row["P2ELO"];
		$i ++;
	}
	echo json_encode($jsonResults);
?>
