<?php
	class BotResult
	{
		public $botid;
		public $botname;
		public $author;
		public $race;
		public $matches;
		public $wins;
		public $winpct;
	}
	function cmp($a, $b)
	{
		if ($a->winpct == $b->winpct) {
			return 0;
		}
		return ($a->winpct < $b->winpct) ? 1 : -1;
	}

header('Content-Type: text/html; charset=utf-8');
?>
<html class=''>
<head>
<title> Starcraft 2 AI Ladder </title>
<link rel="stylesheet" href="responsetable.css" type="text/css" />
</head><body>
<h1>Starcraft 2 Ladder Results</h1>
<?php

	
	$resultsArray = Array();
	
	
	$link = new mysqli("localhost", "root", "", "sc2ladders");
 
	// Check connection
	if($link->connect_error){
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}
	$sql = "SELECT * FROM `participants`";
	$result = $link->query($sql);
	while($row = $result->fetch_array(MYSQLI_ASSOC))
	{
		$Nextbot = new BotResult();
		$Nextbot->botid = $row['ID'];
		$Nextbot->botname = $row['Name'];
		$Nextbot->author= $row['Author'];
		switch($row['Race'])
		{
			case 0:
			
				$Nextbot->race = "Terran";
				break;
			case 1:
				$Nextbot->race = "Zerg";
				break;
			case 2:
				$Nextbot->race = "Protoss";
				break;
			default:
				die("Unknown race" . $row['Race']);
		
		}
		$sql = "SELECT COUNT(*) AS 'Matches' FROM `results` WHERE (Bot1 = '" . $row['ID'] . "' OR Bot2 = '" . $row['ID'] . "')" ;
		$participantResult  = $link->query($sql);
		if($participantRow = $participantResult->fetch_array(MYSQLI_ASSOC))
		{
			$Nextbot->matches = $participantRow['Matches'];
		}
		else
		{
			$Nextbot->matches = 0;
		}
		$sql = "SELECT COUNT(*) AS 'Wins' FROM `results` WHERE `Winner` = '" . $row['ID'] . "'";
		$winsResult = $link->query($sql);
		if($winsRow = $winsResult->fetch_array(MYSQLI_ASSOC))
		{
			$Nextbot->wins = $winsRow['Wins'];
		}
		else
		{
			$Nextbot->wins = 0;
		}
		if($Nextbot->matches == 0 || $Nextbot->wins == 0)
		{
			$Nextbot->winpct = 0;
		}
		else
		{
			$Nextbot->winpct = ( $Nextbot->wins / $Nextbot->matches) * 100;
		}
		$resultsArray[] = $Nextbot;
	}
	usort($resultsArray, "cmp");
	
	?>
	<table class="responstable">
	<tr>
    <th>BotName</th>
    <th>Author</th>
    <th>Race</th>
    <th>Matches</th>
    <th>Wins</th>
    <th>Win Pct</th>
  </tr>
  <?php
  foreach ($resultsArray as $Bot)
  {
	  echo "
  <tr>
    <td><a href=\"botmatches.php?id=" . $Bot->botid . "\">" . $Bot->botname . "</a></td>
    <td>" . $Bot->author . "</td>
    <td>" . $Bot->race . "</td>
    <td>" . $Bot->matches . "</td>
    <td>" . $Bot->wins . "</td>
    <td>" . number_format((float)$Bot->winpct, 2, '.', '') . "</td>
  </tr>";
  }

   ?>
  </table>
 <?php
  $sql = "SELECT max(date) AS 'LastDate' from `results`";
  $LastDateResult = $link->query($sql);
  if($LastDateRow = $LastDateResult->fetch_array(MYSQLI_ASSOC))
  {
	  echo "Last result recieved : " . $LastDateRow['LastDate'] . "<br>";
  }
  ?>

<br>
To get involved, come join us in <a href="https://discord.gg/qTZ65sh">Discord</a>  or <a href="mailto:martin@sc2ai.net">email</a>
<br>
All software used to produce this is open source and available on <a href="https://github.com/Cryptyc/Sc2LadderServer">Github</a>
  </html>