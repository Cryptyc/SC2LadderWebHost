<?php
require_once('Rating.php');
$replay_dir = 'replays';
require_once("dbconf.php");
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

header('Content-Type: text/html; charset=utf-8');
echo "<html> Welcome to bot uploader";
/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
	$link = new mysqli($host, $username, $password , $db_name);
 
// Check connection
if($link->connect_error){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
 
// Attempt insert query execution

	if(!isset($_REQUEST))
	{
	    throw new RuntimeException('Invalid parameters.');
	}
	$BOT1ID = 0;
	$Bot2ID = 0;
	$sql = "SELECT * FROM `participants` WHERE `Name` = '" . mysqli_real_escape_string($link, $_REQUEST['Bot1Name']) . "' AND Race = '" . mysqli_real_escape_string($link, $_REQUEST['Bot1Race']) . "'";
	echo $sql;
	echo "\n<br>\n";
	$result = $link->query($sql);
	if($result->num_rows > 0)
	{
		$row = $result->fetch_assoc();
		echo $sql;
		$BOT1ID = $row['ID'];
		echo "\n<br>\nBot id" . $BOT1ID;
		$Bot1ELO = $row['CurrentELO'];
		if($Bot1ELO == 0)
		{
			$Bot1ELO = 1200;
		}
	}
	else
	{
		$sql = "INSERT INTO `participants` (`Name`, `Race`, `EloFormat`) VALUES ('" . mysqli_real_escape_string($link, $_REQUEST['Bot1Name']) . "', '" . mysqli_real_escape_string($link, $_REQUEST['Bot1Race']) . "', '1')";
		$result = $link->query($sql);
		$BOT1ID = $link->insert_id;
		echo $sql;
		echo "\n<br>\n";
		$Bot1ELO = 1200;
	}
	$sql = "SELECT * FROM `participants` WHERE `Name` = '" . mysqli_real_escape_string($link, $_REQUEST['Bot2Name']) . "' AND Race = '" . mysqli_real_escape_string($link, $_REQUEST['Bot2Race']) . "'";
	$result = $link->query($sql);
	if($result->num_rows > 0)
	{
		$row = $result->fetch_assoc();
		$Bot2ID = $row['ID'];
		echo "\n<br>\nBot id" . $Bot2ID;
		$Bot2ELO = $row['CurrentELO'];
		if($Bot2ELO == 0)
		{
			$Bot2ELO = 1200;
		}

	}
	else
	{
		$sql = "INSERT INTO `participants` (`Name`, `Race`, `EloFormat`) VALUES ('" . mysqli_real_escape_string($link, $_REQUEST['Bot2Name']) . "', '" . mysqli_real_escape_string($link, $_REQUEST['Bot2Race']) . "', '1')";
		echo $sql;
		echo "\n<br>\n";
		$result = $link->query($sql);
		$Bot2ID = $link->insert_id;
		$Bot2ELO = 1200;
	}
	if($_REQUEST['Result'] == "Player1Crash" ||  $_REQUEST['Result'] == "Player2Win")
	{
		echo "Player 2 Win";
		$Winner = 1;
	}
	else if ($_REQUEST['Result'] == "Player2Crash" ||  $_REQUEST['Result'] == "Player1Win")
	{
		echo "Player 1 Win";
		$Winner = 2;
	}
	else if ($_REQUEST['Result'] == "Tie" ||  $_REQUEST['Result'] == "Timeout")
	{
		echo "Draw";
		$Winner = 0;
	}
	else
	{
		echo "Error";
		$Winner = -1;
	}
	if($Winner == 1)
	{
		$Winner = $BOT1ID;
		$Rating1 = Rating::WIN;
		$Rating2 = Rating::LOST;
	}
	else if($Winner == 2)
	{
		$Winner = $Bot2ID;
		$Rating1 = Rating::LOST;
		$Rating2 = Rating::WIN;
	}
	else if($Winner == 0) {
		$Winner = 0;
		$Rating1 = Rating::DRAW;
		$Rating2 = Rating::DRAW;

	}
	if($Winner >= 0)
	{
		$rating = new Rating($Bot1ELO, $Bot2ELO, $Rating1, $Rating2);
		$results = $rating->getNewRatings();
		$Bot1Change = $results['a'] - $Bot1ELO;
		$Bot2Change = $results['b'] - $Bot2ELO;
		echo "ELO: " . $Bot1ELO . "  " . $Bot2ELO;
		$sql = "UPDATE `participants` SET `CurrentELO` = '" . $results['a'] . "' WHERE ID = '" . $BOT1ID . "'";
		echo $sql;
		$result = $link->query($sql);
		$sql = "UPDATE `participants` SET `CurrentELO` = '" . $results['b'] . "' WHERE ID = '" . $Bot2ID . "'";
		echo $sql;
		$result = $link->query($sql);
	}
	else
	{
		$Bot1Change = 0;
		$Bot2Change = 0;
	}

	$sql = "INSERT INTO `results` (`Bot1`, `Bot2`, `Map`, `Date`,`Winner`, `Result`, `Bot1Change`, `Bot2Change` ) VALUES ('" . $BOT1ID. "', '" . $Bot2ID . "', '" . mysqli_real_escape_string($link, $_REQUEST['Map']) . "', NOW(), '" . $Winner . "',  '" . mysqli_real_escape_string($link, $_REQUEST['Result']) . "','" . $Bot1Change . "','" . $Bot2Change . "')";
			echo $sql;
		echo "\n<br>\n";
	$result = $link->query($sql);
	echo $link->error;
	
	if(isset($_FILES['replayfile']))
	{
		$MatchID = $link->insert_id;
		if($MatchID > 0)
		{
			$replayFile = $MatchID . $_REQUEST['Bot1Name'] . "-v-" . $_REQUEST['Bot2Name'] . "-" . $_REQUEST['Map'] . ".Sc2Replay";
			$tmp_name = $_FILES["replayfile"]["tmp_name"];
			$name = basename($replayFile);
			move_uploaded_file($tmp_name, $replay_dir . "/" . $name);
			$sql = "UPDATE `results` SET `ReplayFile`='" . mysqli_real_escape_string($link, $replay_dir . "/" . $name) . "' WHERE `GameID`='" . $MatchID . "'";
			echo $sql;
			$result = $link->query($sql);
		}
	}
	
//	UpdateSeason($BOT1ID, $link);
//	UpdateSeason($Bot2ID, $link);


// Close connection
mysqli_close($link);



?>