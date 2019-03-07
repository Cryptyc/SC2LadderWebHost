<?php
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
$sql = "SELECT * FROM `matches` ORDER BY `MatchTime` DESC";
$result = $link->query($sql);
$LastId = 0;
if($firstrow = $result->fetch_array(MYSQLI_ASSOC))
{
	$LastId = $firstrow["Bot1ID"];
}
$sql = "SELECT * FROM `participants` WHERE `Deactivated`='0' AND `Deleted`='0' AND `ID` > '" . $LastId . "' ORDER BY `ID` ASC LIMIT 1";
$firstResult = $link->query($sql);
$firstrow = $firstResult->fetch_array(MYSQLI_ASSOC);
if(!$firstrow)
{
	$sql = "SELECT * FROM `participants` WHERE `Deactivated`='0' AND `Deleted`='0' ORDER BY `ID` ASC LIMIT 1";
	$firstResult = $link->query($sql);
	$firstrow = $firstResult->fetch_array(MYSQLI_ASSOC);
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
	$sql .= " ORDER BY RAND() LIMIT 1";
	$secondResult = $link->query($sql);
	if($secondrow = $secondResult->fetch_array(MYSQLI_ASSOC))
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