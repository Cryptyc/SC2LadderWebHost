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
	$sql = "SELECT * FROM `participants`";
	$result = $link->query($sql);
	$i = 0;
	while($row = $result->fetch_assoc())
	{
		$jsonResults["Bots"][$i]["id"] = $row["ID"];
		$jsonResults["Bots"][$i]["name"] = $row["Name"];
		$jsonResults["Bots"][$i]["race"] = $races[$row["Race"]];
		$jsonResults["Bots"][$i]["elo"] = $row["CurrentELO"];
		if($row["Deactivated"] == 1)
		{
			$jsonResults["Bots"][$i]["deactivated"] = true;
		}
		else
		{
			$jsonResults["Bots"][$i]["deactivated"] = false;
		}
		if($row["Deleted"] == 1)
		{
			$jsonResults["Bots"][$i]["deleted"] = true;
		}
		else
		{
			$jsonResults["Bots"][$i]["deleted"] = false;
		}
		
		$i ++;
	}
	echo json_encode($jsonResults);
?>
