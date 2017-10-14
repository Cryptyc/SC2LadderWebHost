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
		public $winpct;
	}
	function cmp($a, $b)
	{
		if ($a->winpct == $b->winpct) {
			return 0;
		}
		return ($a->winpct < $b->winpct) ? 1 : -1;
	}


	$CurrentSeason = 1;
	if(isset($_REQUEST['season']))
	{
		$CurrentSeason = $_REQUEST['season'];
		$sql = "SELECT * FROM `seasonids` WHERE `id` = '" . mysqli_real_escape_string($link, $_REQUEST['season']) . "'";
	}
	else
	{
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
                       <div class="header">
						<h3> Season: <?php echo $SeasonName; ?></h3>
                        </div>
			<table class="table table-striped">
	<tr>
    <th>BotName</th>
    <th>Author</th>
    <th>Race</th>
    <th>Matches</th>
    <th>Wins</th>
    <th>Win Pct</th>
    <th></th>
	
  </tr>

<?php
	
	

	$sql = "SELECT `participants`.`Name` AS Name, 
			`participants`.`ID` AS ID,
			`participants`.`Race` AS Race,
			`members`.`username` AS username,
			`members`.`Alias` AS Alias
			FROM `participants`, `members`
			WHERE `participants`.`Author` = `members`.`id`
			AND `participants`.`Verified` = '1'
			AND `participants`.`Deactivated` = '0'
			AND `participants`.`Deleted` = '0'";
	$result = $link->query($sql);
	while($row = $result->fetch_array(MYSQLI_ASSOC))
	{
		$Nextbot = new BotResult();
		$Nextbot->botid = $row['ID'];
		$Nextbot->botname = $row['Name'];
		if($row['Alias'] == "")
		{
			$Nextbot->author= $row['username'];
		}
		else
		{
			$Nextbot->author= $row['Alias'];
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
			default:
				die("Unknown race" . $row['Race']);
		
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
	
  foreach ($resultsArray as $Bot)
  {
	  if($Bot->matches > 0)
	  {
	  echo "
  <tr>
    <td><a href=\"botmatches.php?id=" . $Bot->botid . "\">" . $Bot->botname . "</a></td>
    <td>" . $Bot->author . "</td>
    <td>" . $Bot->race . "</td>
    <td>" . $Bot->matches . "</td>
    <td>" . $Bot->wins . "</td>
    <td>" . number_format((float)$Bot->winpct, 2, '.', '') . "</td>
    <td> <button type=\"button\" id=\"Details\" class=\"btn btn-info navbar-btn\" onclick=\"window.location.href='botmatches.php?id=" . $Bot->botid . "&season=" . $CurrentSeason . "'\">
                                <span>View Matches</span>
                            </button></td>
  </tr>";
	  }
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
  require_once("footer.php");
  ?>



  </body>
</html>
