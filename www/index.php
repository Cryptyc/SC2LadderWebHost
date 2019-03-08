<?php
session_start();
require_once("header.php");

	class BotResult
	{
		public $botid;
		public $botname;
		public $author;
		public $race;
		public $matches;
		public $wins;
		public $crashes;
		public $ELO;
	}
	function cmp($a, $b)
	{
		if ($a->ELO == $b->ELO) {
			return 0;
		}
		return ($a->ELO < $b->ELO) ? 1 : -1;
	}
	
	function patreonlevel($innum)
	{
		switch($innum)
		{
			case 0:
				return "<img height=\"20\" width=\"20\" src=\"./images/no-star.png\">";
			case 1:
				return "<a href=\"https://www.patreon.com/Starcraft2AI\"><img height=\"20\" width=\"20\" src=\"./images/bronze-star.png\"></a>";
			case 2:
				return "<a href=\"https://www.patreon.com/Starcraft2AI\"><img height=\"20\" width=\"20\" src=\"./images/silver-star.png\"></a>";
			case 3:
				return "<a href=\"https://www.patreon.com/Starcraft2AI\"><img height=\"20\" width=\"20\" src=\"./images/gold-star.png\"></a>";
			default:
				return "";
		}
	}

	$CurrentSeason = 1;
	if(isset($_REQUEST['season']))
	{
		$CurrentSeason = $_REQUEST['season'];
		$sql = "SELECT * FROM `seasonids` WHERE `id` = '" . mysqli_real_escape_string($link, $_REQUEST['season']) . "'";
	}
	else
	{
		$ShowTournament = 1;
		$sql = "SELECT * FROM `seasonids` WHERE `Current` = '1'";
	}
	$result = $link->query($sql);
	if($row = $result->fetch_assoc())
	{
		$CurrentSeason = $row['id'];
		$SeasonName = $row['SeasonName'];
	}

	$resultsArray = Array();
?>
<?php
echo $row['TournamentResults'];
?>
			<table class="table table-striped">
	<tr>
    <th>Pos</th>
    <th>BotName</th>
    <th>Author</th>
    <th>Race</th>
    <th>Matches</th>
    <th>Wins</th>
<?php
if ($CurrentSeason < 5)
{
	echo "<th>WinPct</th>";
}
else 
{
    echo "<th>ELO</th>";
}
?>
    <th></th>

  </tr>

<?php
	$sql = "SELECT `participants`.`Name` AS Name,
			`participants`.`ID` AS ID,
			`participants`.`Race` AS Race,
			`participants`.`CurrentELO` AS ELO,
			`members`.`username` AS username,
			`members`.`Alias` AS Alias,
			`members`.`Patreon` AS Patreon
			FROM `participants`, `members`
			WHERE `participants`.`Author` = `members`.`id`";
	$result = $link->query($sql);
	while($row = $result->fetch_array(MYSQLI_ASSOC))
	{
		$Nextbot = new BotResult();
		$Nextbot->botid = $row['ID'];
		$Nextbot->botname = htmlspecialchars($row['Name']);
		$Nextbot->patreon = $row['Patreon'];
		if($row['Alias'] == "")
		{
			$Nextbot->author= htmlspecialchars($row['username']);
		}
		else
		{
			$Nextbot->author= htmlspecialchars($row['Alias']);
		}
		
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
			case 3:
				$Nextbot->race = "Random";
				break;
			default:
				$Nextbot->race = "Unnkown";

//				die("Unknown race" . $row['Race']);

		}
		$sql = "SELECT COUNT(*) AS 'Matches' FROM `results` WHERE SeasonId = '" . $CurrentSeason . "' AND (Bot1 = '" . $row['ID'] . "' OR Bot2 = '" . $row['ID'] . "')" ;
		$participantResult  = $link->query($sql);
		if($participantRow = $participantResult->fetch_array(MYSQLI_ASSOC))
		{
			$Nextbot->matches = $participantRow['Matches'];
		}
		else
		{
			$Nextbot->matches = 0;
		}
		$sql = "SELECT COUNT(*) AS 'Wins' FROM `results` WHERE SeasonId = '" . $CurrentSeason . "' AND `Winner` = '" . $row['ID'] . "'";
		$winsResult = $link->query($sql);
		if($winsRow = $winsResult->fetch_array(MYSQLI_ASSOC))
		{
			$Nextbot->wins = $winsRow['Wins'];
		}
		else
		{
			$Nextbot->wins = 0;
		}
		if($CurrentSeason < 5)
		{
			if($Nextbot->matches == 0 || $Nextbot->wins == 0)
			{
				$Nextbot->ELO = 0;
			}
			else
			{
				$Nextbot->ELO = round(( $Nextbot->wins / $Nextbot->matches) * 100, 2);
			}
		}
		else
		{
			if($CurrentSeason < 7)
			{
				$sql = "SELECT `ELO` FROM `seasons` WHERE `Season` = '" . $CurrentSeason . "' AND `BotId` = '" . $row['ID'] . "'";
				$ELOResult = $link->query($sql);
				if($ELORow = $ELOResult->fetch_array(MYSQLI_ASSOC))
				{
					$Nextbot->ELO = $ELORow['ELO'];
				}				
			}
			else
			{
				if($row['ELO'] == 0)
				{
					$Nextbot->ELO = 1200;
				}
				else
				{
					$Nextbot->ELO = $row['ELO'];
				}
			}
		}
		$resultsArray[] = $Nextbot;
	}
	usort($resultsArray, "cmp");
  $i = 1;
  foreach ($resultsArray as $Bot)
  {
	  if($Bot->matches > 0)
	  {
	  echo "
  <tr>
    <td>" . $i . "</td>
	<td><a href=\"BotProfile.php?BotId=" . $Bot->botid . "&season=" . $CurrentSeason ."\">" . $Bot->botname . "</a></td>
    <td>" . $Bot->author . "&nbsp;" . patreonlevel($Bot->patreon) . "</td>
    <td>" . $Bot->race . "</td>
    <td>" . $Bot->matches . "</td>
    <td>" . $Bot->wins . "</td>
    <td>" . $Bot->ELO . "</td>
    <td> <button type=\"button\" id=\"Details\" class=\"btn btn-info navbar-btn\" onclick=\"window.location.href='botmatches.php?id=" . $Bot->botid . "&season=" . $CurrentSeason . "'\">
                                <span>View Matches</span>
                            </button></td>
  </tr>";
		$i ++;
	  }
  }

   ?>
  </table>
 <?php
  $sql = "SELECT max(date) AS 'LastDate', ROUND((UNIX_TIMESTAMP() - UNIX_TIMESTAMP(max(date))) / 60) as 'Minutes' from `results`";

  $LastDateResult = $link->query($sql);
  if($LastDateRow = $LastDateResult->fetch_array(MYSQLI_ASSOC))
  {
	  echo "Last result received : " . $LastDateRow['LastDate'] . " UTC (" . $LastDateRow['Minutes'] . " minutes ago)<br>";
  }
  $sql = "SELECT Count(GameID) AS results FROM results WHERE date >= now() - INTERVAL 1 DAY";
  $LastDateResult = $link->query($sql);
  if($LastDateRow = $LastDateResult->fetch_array(MYSQLI_ASSOC))
  {
	  echo "Results in last 24hours: " . $LastDateRow['results'] . "<br>";
  }
  require_once("footer.php");
  ?>



  </body>
</html>
