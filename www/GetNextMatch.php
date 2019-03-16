<?php
$races[0] = "Terran";
$races[1] = "Zerg";
$races[2] = "Protoss";
$races[3] = "Random";
$skipping_per = 20;  //percentage of battles skipped to create opponent mixture.
$new_skipping_per = 50;  //percentage of battles skipped to create opponent mixture.

require_once("dbconf.php");
$link = new mysqli($host, $username, $password , $db_name);

// Check connection
if($link->connect_error){
	die("ERROR: Could not connect. " . mysqli_connect_error());
}
$sql = "SELECT * FROM `members` WHERE `username` = '" . mysqli_real_escape_string($link, $_REQUEST["Username"]) . "'";
$usersResult = $link->query($sql);
if(!$usersrow = $usersResult->fetch_array(MYSQLI_ASSOC))
{
	echo "User no verified for requesting matches";
	exit();
}

if(!password_verify($_REQUEST["Password"], $usersrow["password"]))
{
	echo "User no verified for requesting matches";
	exit();
}
$sql = "SELECT * FROM `participants` WHERE `Deactivated`='0' AND `Deleted`='0' ORDER BY RAND()";
$firstrow;
$oldest = 0;
$result = $link->query($sql);
while($row = $result->fetch_array())
{
	$sql = "SELECT * FROM `matches` WHERE `Bot1ID`='" .$row["ID"] . "' OR `Bot2ID`='" .$row["ID"] . "'";
	$sql .= " ORDER BY `MatchTime` DESC LIMIT 1";
	$match_result = $link->query($sql);
	if($match = $match_result->fetch_array(MYSQLI_ASSOC))
	{
		if ($oldest == 0 || $match['MatchTime'] < $oldest)
		{
			$oldest = $match['MatchTime'];
			$firstrow = $row;
		}
	}
	else
	{
		$firstrow = $row;
		break;
	}
}
if($firstrow)
{
	$sql = "SELECT * FROM `participants` WHERE `Deactivated`='0' AND `Deleted`='0' AND `ID` != '" .$firstrow["ID"] . "'";
	if($firstrow["CurrentELO"] > 0)
	{
		$minELO = $firstrow["CurrentELO"] - 300;
		$maxELO = $firstrow["CurrentELO"] + 300;
		$sql .= " AND ((`CurrentELO` > '" . $minELO . "' AND `CurrentELO` < '" . $maxELO . "') OR `CurrentELO`='0')";
	}
	$sql .= " ORDER BY RAND()";
	$eligible = $link->query($sql);
	$secondrow;
	$best_opp_last = 0;
	while($row = $eligible->fetch_array())
	{
		$opp_id = $row['ID'];
		$sql = "SELECT * FROM `matches` WHERE (`Bot1ID`='" .$firstrow["ID"] . "' AND `Bot2ID`='" .$row["ID"] . "')";
		$sql .= " OR (`Bot1ID`='" .$row["ID"] . "' AND `Bot2ID`='" .$firstrow["ID"] . "')";
		$sql .= " ORDER BY `MatchTime` DESC LIMIT 1";
		$opp_result = $link->query($sql);
		if($match = $opp_result->fetch_array(MYSQLI_ASSOC))
		{
			if (rand(0,100) <= $skipping_per) {
				continue;
			}
			if ($best_opp_last == 0 || $match['MatchTime'] < $best_opp_last)
			{
				$best_opp_last = $match['MatchTime'];
				$secondrow = $row;
			}
		}
		else
		{
			if (rand(0,100) <= $new_skipping_per) {
				continue;
			}			
			$secondrow = $row;
			break;
		}
	}
	if($secondrow)
	{
		$sql  = "SELECT * FROM `maps` WHERE `Active`='1' ORDER BY RAND() LIMIT 1";
		$mapsResult = $link->query($sql);
		if($maprow = $mapsResult->fetch_array(MYSQLI_ASSOC))
		{
			$jsonOut["Bot1"]["name"] = $firstrow["Name"];
			$jsonOut["Bot1"]["race"] = $races[$firstrow["Race"]];
			$jsonOut["Bot1"]["elo"] = $firstrow["CurrentELO"];
			$jsonOut["Bot1"]["playerid"] = $firstrow["PlayerID"];
			$jsonOut["Bot1"]["checksum"] = md5_file($firstrow["WorkingDirectory"]);
			if($firstrow["DataDirectory"] != "" && file_exists($_SERVER["DOCUMENT_ROOT"] . '/' . $firstrow["DataDirectory"]))
			{
				$jsonOut["Bot1"]["datachecksum"] = md5_file($_SERVER["DOCUMENT_ROOT"] . '/' . $firstrow["DataDirectory"]);
			}
			$jsonOut["Bot2"]["name"] = $secondrow["Name"];
			$jsonOut["Bot2"]["race"] = $races[$secondrow["Race"]];
			$jsonOut["Bot2"]["elo"] = $secondrow["CurrentELO"];
			$jsonOut["Bot2"]["playerid"] = $secondrow["PlayerID"];
			$jsonOut["Bot2"]["checksum"] = md5_file($secondrow["WorkingDirectory"]);
			if($secondrow["DataDirectory"] != "" && file_exists($_SERVER["DOCUMENT_ROOT"] . '/' . $secondrow["DataDirectory"]))
			{
				$jsonOut["Bot2"]["datachecksum"] = md5_file($_SERVER["DOCUMENT_ROOT"] . '/' . $secondrow["DataDirectory"]);
			}
			$jsonOut["Map"] = $maprow["MapName"];
			$sql = "INSERT INTO `matches` (`Bot1ID`, `Bot2ID`, `MapName`, `RequesterId`) VALUES ('" . $firstrow["ID"] . "' ,'" . $secondrow["ID"] . "','" . $maprow["MapName"] . "','" . $usersrow["id"] . "')";
			$link->query($sql);
			echo json_encode($jsonOut);
			exit();
		}
	}
}
echo "Unable to get match";
exit();

?>