<?php
session_start();
require_once("header.php")
?>
	<table>
	<tr>
	<th>Bot Name</th>
	<th>Avg Game Time</th>
	<th>Avg Frame Time</th>
	</tr>
<?php
	$sql = "SELECT * FROM `participants` WHERE `CurrentELO` > 0";
	$result = $link->query($sql);
	
	while($row = $result->fetch_array(MYSQLI_ASSOC))
	{
		$resSql = "SELECT * FROM `results` 
		WHERE SeasonId ='6'
		AND (`results`.Bot1='" . $row["ID"] . "' OR `results`.`Bot2`='" . $row["ID"] . "')
		AND (`results`.Bot1AvgFrame > 0 OR `results`.`Bot2AvgFrame` > 0)";
		$BotResult = $link->query($resSql);
		$currentTot = 0;
		$currentNum = 0;
		$currentFrames = 0;
		$currentFrameNum = 0;
		while($BotRow = $BotResult->fetch_array(MYSQLI_ASSOC))
		{
			
			if($row["ID"] == $BotRow["Bot1"])
			{
				if($BotRow["Bot1AvgFrame"] > 0)
				{
					$currentTot = $currentTot + $BotRow["Bot1AvgFrame"];
					$currentNum ++;
				}
			}
			else
			{
				if($BotRow["Bot2AvgFrame"] > 0)
				{
					$currentTot = $currentTot + $BotRow["Bot2AvgFrame"];
					$currentNum ++;
				}
			}
			if($BotRow["Frames"] > 0)
			{
				$currentFrames = $currentFrames + $BotRow["Frames"];
				$currentFrameNum ++;
			}
		}
		if($currentFrameNum > 0 && $currentNum > 0)
		{
			echo "<tr><td>" . $row["Name"] . "</td><td>";
			if($currentFrameNum > 0)
			{
				$avgFrames = $currentFrames / $currentFrameNum;
				$seconds = $avgFrames / 22.4;
				echo gmdate("H:i:s", $seconds);
			}
			echo "</td><td>";
			if($currentNum > 0)
			{
				$BotAvg = $currentTot / $currentNum;
				echo number_format($BotAvg, 2);
			}
			echo "<td></tr>";
		}
	}
	?>