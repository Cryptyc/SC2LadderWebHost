<?php
	class BotResult
	{
		public $botname;
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

echo "<html>";
echo "<table>";

	
	$resultsArray = Array();
	
	
	$link = new mysqli("localhost", "root", "", "sc2ladders");
 
	// Check connection
	if($link->connect_error){
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}
	$sql = "SELECT * FROM `Participants`";
	$result = $link->query($sql);
	while($row = $result->fetch_array(MYSQLI_ASSOC))
	{
		$Nextbot = new BotResult();
		$Nextbot->botname = $row['Name'];
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
	<tr>
    <th>BotName</th>
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
    <td>" . $Bot->botname . "</td>
    <td>" . $Bot->race . "</td>
    <td>" . $Bot->matches . "</td>
    <td>" . $Bot->wins . "</td>
    <td>" . $Bot->winpct . "</td>
  </tr>";
  }
   ?>
  </table>
  </html>