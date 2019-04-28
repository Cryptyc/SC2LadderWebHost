<?php
session_start();
	require_once("header.php");

	class BotRecord
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


	if(!isset($_REQUEST['BotId']))
	{
		die("ERROR: invalid request");
	}
	$sql = "SELECT 
		`participants`.`ID` AS BotID,
		`participants`.`Name` AS BotName,
		`participants`.`CurrentELO` AS ELO,
		`participants`.`Downloadable` AS Downloadable,
		`participants`.`DataDirectory` AS DataDirectory,
		`members`.`Avatar` AS Avatar,
		`members`.`Github` AS Github,
		`members`.`Website` AS Website,
		`members`.`username` AS username,
		`botrequests`.`FileLoc` AS FileLoc,
		`botrequests`.`UploadedTime` AS UploadedTime
		FROM `members`, `botrequests`, `participants` 
		WHERE `members`.`id` = `participants`.`Author` AND `botrequests`.`id` = `participants`.`ID` AND `participants`.`ID` = '". mysqli_real_escape_string($link, $_REQUEST['BotId']) . "'";
	$result = $link->query($sql);
	if(!$row = $result->fetch_assoc())
	{
		die("unable to get profile" . $sql);
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
	if($seasonrow = $result->fetch_assoc())
	{
		$CurrentSeason = $seasonrow['id'];
		$SeasonName = $seasonrow['SeasonName'];
	}
	$sql = "SELECT COUNT(*) AS 'Matches' FROM `results` WHERE SeasonId = '" . $CurrentSeason . "' AND (Bot1 = '" . mysqli_real_escape_string($link, $_REQUEST['BotId']) . "' OR Bot2 = '" . mysqli_real_escape_string($link, $_REQUEST['BotId']) . "')" ;
	$participantResult  = $link->query($sql);
	if($participantRow = $participantResult->fetch_array(MYSQLI_ASSOC))
	{
		$BotMatches = $participantRow['Matches'];
	}
	else
	{
		$BotMatches = 0;
	}
	$sql = "SELECT COUNT(*) AS 'Wins' FROM `results` WHERE SeasonId = '" . $CurrentSeason . "' AND `Winner` = '" . mysqli_real_escape_string($link, $_REQUEST['BotId']) . "'";
	$winsResult = $link->query($sql);
	if($winsRow = $winsResult->fetch_array(MYSQLI_ASSOC))
	{
		$BotWins = $winsRow['Wins'];
	}
	else
	{
		$BotWins = 0;
	}

	?>

<hr>
<div class="container bootstrap snippet">
    <div class="row">
  		<div class="col-sm-10"><h1><?php echo htmlspecialchars($row['BotName']); ?></h1></div>
    	<div class="col-sm-2">
		<?php
		if( $row['Avatar'] == "")
		{
			echo "<img title=\"profile image\" class=\"img-circle img-responsive\" src=\"./images/avatar.jpg\">";
		}
		else
		{
			echo "<img title=\"profile image\" class=\"img-circle img-responsive\" src=\"" . $row['Avatar'] . "\">";
		}
		?>
		</div>
    </div>
    <div class="row">
  		<div class="col-sm-3"><!--left col-->
              
          <ul class="list-group">
            <li class="list-group-item text-muted">Profile</li>
            <li class="list-group-item text-right"><span class="pull-left"><strong>Games</strong></span> <?php echo htmlspecialchars($BotMatches); ?></li>
            <li class="list-group-item text-right"><span class="pull-left"><strong>Wins</strong></span> <?php echo htmlspecialchars($BotWins); ?></li>
            <li class="list-group-item text-right"><span class="pull-left"><strong>Current ELO</strong></span> <?php echo htmlspecialchars($row['ELO']); ?></li>
            <li class="list-group-item text-right"><span class="pull-left"><strong>Last Update</strong></span> <?php echo htmlspecialchars($row['UploadedTime']); ?></li>
			<?php
			if($row['Downloadable'] == 1)
			{
			?>
				<li class="list-group-item text-right"><span class="pull-left"><strong>Download</strong></span><?php echo "<a href=\"" . htmlspecialchars($row['FileLoc']) . "\">Available</a>"; ?></li>
			<?php
			}
			else
			{
			?>
				<li class="list-group-item text-right"><span class="pull-left"><strong>Download</strong></span>Unavailable</li>
			<?php
			}
			if(($_SESSION['username'] == $row['username'] || $_SESSION['Admin'] == 1) && $row["DataDirectory"] != "" && file_exists($_SERVER["DOCUMENT_ROOT"] . "/" . $row["DataDirectory"]))
			{
			?>
				<li class="list-group-item text-right"><span class="pull-left"><strong>Download Data</strong></span><?php echo "<a href=\"DataDownload.php?BotName=" . htmlspecialchars($row['BotName']) . "\">Available</a>"; ?></li>
			<?php
			}

			?>
            
          </ul> 
               
          <div class="panel panel-default">
            <div class="panel-heading">Website <i class="fa fa-link fa-1x"></i></div>
            <div class="panel-body"><?php echo "<a href=\"" . htmlspecialchars($row['Website']) . "\">" . htmlspecialchars($row['Website']) . "</a>" ?></div>
          </div>
          
          <div class="panel panel-default">
            <div class="panel-heading">Github <i class="fa fa-link fa-1x"></i></div>
            <div class="panel-body"><?php echo "<a href=\"" . htmlspecialchars($row['Github']) . "\">" . htmlspecialchars($row['Github']) . "</a>" ?></div>
          </div>
          
        </div><!--/col-3-->
    	<div class="col-sm-9">
          <div class="tab-content">
            <div class="tab-pane active" id="home">
			  <div class="panel-heading"><h2>Opponent Stats</h2></div>
<?php

	function getOppStats($bot_id, $opp_id, $link) {
		$opp_total_matches = 0;
		$sql = "SELECT COUNT(*) as Matches FROM results WHERE Bot1='" . $opp_id . "' OR Bot2='" . $opp_id . "' LIMIT 1";
		$result  = $link->query($sql);
		if($opp_row = $result->fetch_array(MYSQLI_ASSOC)) {
			$opp_total_matches = $opp_row['Matches'];
		}
		$opp_vs_matches = 0;
		$sql = "SELECT COUNT(*) as Matches FROM results WHERE (Bot1='" . $opp_id . "' AND Bot2='" . $bot_id . "') OR (Bot1='" . $bot_id . "' AND Bot2='" . $opp_id . "') LIMIT 1";
		$result  = $link->query($sql);
		if($opp_row = $result->fetch_array(MYSQLI_ASSOC)) {
			$opp_vs_matches = $opp_row['Matches'];
		}
		$opp_total_wins = 0;
		$sql = "SELECT COUNT(*) as Wins FROM results WHERE Winner = '" . $opp_id . "' LIMIT 1";
		$result  = $link->query($sql);
		if($opp_row = $result->fetch_array(MYSQLI_ASSOC)) {
			$opp_total_wins = $opp_row['Wins'];
		}
		$opp_vs_wins = 0;
		$sql = "SELECT COUNT(*) as Wins FROM results WHERE ((Bot1='" . $opp_id . "' AND Bot2='" . $bot_id . "') OR (Bot1='" . $bot_id . "' AND Bot2='" . $opp_id . "')) AND Winner = '" . $bot_id . "' LIMIT 1";
		$result  = $link->query($sql);
		if($opp_row = $result->fetch_array(MYSQLI_ASSOC)) {
			$opp_vs_wins = $opp_row['Wins'];
		}
		return [$opp_total_matches, $opp_vs_matches, $opp_total_wins, $opp_vs_wins];
	}

	function getOppRadius($elo, $max_elo, $min_elo) {
		return (15 * ($elo - $min_elo) / ($max_elo - $min_elo)) + 5;
	}
	
	function getLastUpdate($bot_id, $link) {
		$last_update = 'Unknown';
		$sql = "SELECT UploadedTime FROM botrequests WHERE id='" . $bot_id . "' LIMIT 1";
		$result  = $link->query($sql);
		if($opp_row = $result->fetch_array(MYSQLI_ASSOC)) {
			$last_update = $opp_row['UploadedTime'];
		}
		return $last_update;
	}
	
	function getLastResult($bot_id, $opp_id, $link) {
		$last_result = 'Unknown';
		$last_date = 'Unknown';
		$sql = "SELECT Winner, Date FROM results WHERE ((Bot1='" . $opp_id . "' AND Bot2='" . $bot_id . "') OR (Bot1='" . $bot_id . "' AND Bot2='" . $opp_id . "')) ORDER BY Date DESC LIMIT 1";
		$result  = $link->query($sql);
		if($opp_row = $result->fetch_array(MYSQLI_ASSOC)) {
			if ($opp_row['Winner'] == 0) {
				$last_result = 'TIE';
			}
			else if ($opp_row['Winner'] == $bot_id) {
				$last_result = 'WIN';
			}
			else {
				$last_result = 'LOSS';
			}
			$last_date = $opp_row['Date'];
		}
		return [$last_result, $last_date];
	}
	
	function getEloExchange($bot_id, $opp_id, $link) {
		$bot1_exchange = 0;
		$sql = "SELECT SUM(Bot1Change) as botsum FROM results WHERE (Bot1='" . $bot_id . "' AND Bot2='" . $opp_id . "') LIMIT 1";
		$result  = $link->query($sql);
		if($opp_row = $result->fetch_array(MYSQLI_ASSOC)) {
			$bot1_exchange = $opp_row['botsum'];
		}
		$bot2_exchange = 0;
		$sql = "SELECT SUM(Bot2Change) as botsum FROM results WHERE (Bot2='" . $bot_id . "' AND Bot1='" . $opp_id . "') LIMIT 1";
		$result  = $link->query($sql);
		if($opp_row = $result->fetch_array(MYSQLI_ASSOC)) {
			$bot2_exchange = $opp_row['botsum'];
		}
		return $bot1_exchange + $bot2_exchange;
	}
	
	function getMaxSeason($link) {
		$season = 0;
		#$sql = "SELECT * FROM `seasonids` WHERE `Current` = '1'";
		$sql = "SELECT MAX(id) as season FROM seasonids WHERE Current = '1' LIMIT 1";
		$result  = $link->query($sql);
		if($opp_row = $result->fetch_array(MYSQLI_ASSOC)) {
			$season = $opp_row['season'];
		}
		return $season;
	}

	if ($CurrentSeason == getMaxSeason($link)) {
	#this if statement is not closed until the end of the chart code.
	#not tabbing over because it will make things messy.
	
	$max_elo = 0;
	$min_elo = 0;
	
	$zerg_data = '';
	$toss_data = '';
	$terr_data = '';
	$rand_data = '';
	
	$zerg_radi = '';
	$toss_radi = '';
	$terr_radi = '';
	$rand_radi = '';
	
	$zerg_bot_data = '';
	$toss_bot_data = '';
	$terr_bot_data = '';
	$rand_bot_data = '';

	$sql = "SELECT MAX(CurrentELO) AS maxELO, MIN(NULLIF(CurrentELO, 0)) AS minELO FROM participants LIMIT 1";
	$result = $link->query($sql);
	while ($mrow = $result->fetch_assoc()) {
		$max_elo = $mrow['maxELO'];
		$min_elo = $mrow['minELO'];
	}
	$rank = 0;
	$sql = "SELECT ID, Name, Race, CurrentELO FROM participants WHERE CurrentELO > 0 ORDER BY CurrentELO DESC";
	$result = $link->query($sql);
	while ($chartrow = $result->fetch_assoc()) {
		$rank++;
		#if it's our own bot, continue to next.
		if ($row['BotID'] == $chartrow['ID']) {
			continue;
		}
		$stats_arr = getOppStats($row['BotID'], $chartrow['ID'], $link);
		$opp_radius = getOppRadius($chartrow['CurrentELO'], $max_elo, $min_elo);
		$opp_update = getLastUpdate($chartrow['ID'], $link);
		$opp_res_arr = getLastResult($row['BotID'], $chartrow['ID'], $link);
		$elo_exchange = getEloExchange($row['BotID'], $chartrow['ID'], $link);
		#chart is actually -50 to +50, not 0-100 as it appears, so adjust down.
		$overall_win = -50; 
		$vs_win = -50;
		#if the bots haven't played each other, skip it.
		if ($stats_arr[1] == 0) {
			continue;
		}
		if ($stats_arr[0] > 0) {
			$overall_win = round(($stats_arr[2] / $stats_arr[0]) * 100, 2) - 50;
		}
		if ($stats_arr[1] > 0) {
			$vs_win = round(($stats_arr[3] / $stats_arr[1]) * 100, 2) - 50;
		}
		$p_overall_win = $overall_win + 50;
		$p_vs_win = $vs_win + 50;
		if ($chartrow['Race'] == 0) {
			#name, overall win%, win%, elo, radius, rank, last_update, last_result, last_result_date
			$terr_info .= "['" .  htmlspecialchars($chartrow['Name']) . "', '$p_overall_win', '$p_vs_win',";
			$terr_info .= "'" . $chartrow['CurrentELO'] . "', '$opp_radius', '$rank', '$opp_update', '" . $opp_res_arr[0] . "',  '" . $opp_res_arr[1] . "', ";
			$terr_info .= "'" . $stats_arr[1] . "', '" . $stats_arr[0] . "', '$elo_exchange'],";

			$terr_data .= "{ x: $overall_win, y: $vs_win },";
			$terr_radi .= "'" . $opp_radius . "',";
		}
		else if ($chartrow['Race'] == 1) {
			$zerg_info .= "['" .  htmlspecialchars($chartrow['Name']) . "', '$p_overall_win', '$p_vs_win',";
			$zerg_info .= "'" . $chartrow['CurrentELO'] . "', '$opp_radius', '$rank', '$opp_update', '" . $opp_res_arr[0] . "',  '" . $opp_res_arr[1] . "', ";
			$zerg_info .= "'" . $stats_arr[1] . "', '" . $stats_arr[0] . "', '$elo_exchange'],";

			$zerg_data .= "{ x: $overall_win, y: $vs_win },";
			$zerg_radi .= "'" . $opp_radius . "',";
		}
		else if ($chartrow['Race'] == 2) {
			$toss_info .= "['" .  htmlspecialchars($chartrow['Name']) . "', '$p_overall_win', '$p_vs_win',";
			$toss_info .= "'" . $chartrow['CurrentELO'] . "', '$opp_radius', '$rank', '$opp_update', '" . $opp_res_arr[0] . "',  '" . $opp_res_arr[1] . "', ";
			$toss_info .= "'" . $stats_arr[1] . "', '" . $stats_arr[0] . "', '$elo_exchange'],";

			$toss_data .= "{ x: $overall_win, y: $vs_win },";
			$toss_radi .= "'" . $opp_radius . "',";
		}
		else  {
			$rand_info .= "['" .  htmlspecialchars($chartrow['Name']) . "', '$p_overall_win', '$p_vs_win',";
			$rand_info .= "'" . $chartrow['CurrentELO'] . "', '$opp_radius', '$rank', '$opp_update', '" . $opp_res_arr[0] . "',  '" . $opp_res_arr[1] . "', ";
			$rand_info .= "'" . $stats_arr[1] . "', '" . $stats_arr[0] . "', '$elo_exchange'],";

			$rand_data .= "{ x: $overall_win, y: $vs_win },";
			$rand_radi .= "'" . $opp_radius . "',";
		}
	}
?>		
<canvas id="oppScatterChart" width="400" height="200"></canvas>
<script>
	
function getColor(value) {
    //value from 0 to 1
    var hue=((100-(value))*120).toString(10);
    return ["hsl(",hue,",100%,50%)"].join("");
}	

function wrapPercent(value) {
	color = getColor(value);
	return '<font style="color:' + color + '">' + value + '</font>';
}


Chart.Legend.prototype.afterFit = function() {
    this.height = this.height + 10;
};
var basebot = '<?php echo htmlspecialchars($row['BotName']); ?>';
var ctx = document.getElementById('oppScatterChart');
var scatterChart = new Chart(ctx, {
    type: 'scatter',
    data: {
        datasets: [
			{
            label: 'Protoss',
            data: [<?php echo $toss_data; ?>],
            info: [<?php echo $toss_info; ?>],
			pointRadius: [<?php echo $toss_radi; ?>],
			pointHoverRadius: [<?php echo $toss_radi; ?>],
			backgroundColor: 'rgba(67, 255, 0, 0.5)'
			
			},				   
			{
            label: 'Terran',
            data: [<?php echo $terr_data; ?>],
            info: [<?php echo $terr_info; ?>],
			pointRadius: [<?php echo $terr_radi; ?>],
			pointHoverRadius: [<?php echo $terr_radi; ?>],
			backgroundColor: 'rgba(72, 0, 255, 0.5)'
			},
			{
            label: 'Zerg',
            data: [<?php echo $zerg_data; ?>],
            info: [<?php echo $zerg_info; ?>],
			pointRadius: [<?php echo $zerg_radi; ?>],
			pointHoverRadius: [<?php echo $zerg_radi; ?>],
			backgroundColor: 'rgba(255, 4, 0, 0.5)'
			},
			{
            label: 'Random',
            data: [<?php echo $rand_data; ?>],
            info: [<?php echo $rand_info; ?>],
			pointRadius: [<?php echo $rand_radi; ?>],
			pointHoverRadius: [<?php echo $rand_radi; ?>],
			backgroundColor: 'rgba(225, 255, 0, 0.5)'
			}]
    },
    options: {
		tooltips: {
			callbacks: {
				label: function(tooltipItem, data) {
					var label = " " + data.datasets[tooltipItem.datasetIndex].info[tooltipItem.index][0] + " ("  + data.datasets[tooltipItem.datasetIndex].label + ")";
					return label;
				},
				afterLabel: function(tooltipItem, data) {
					//name, overallwin%, win%, elo, radius, rank, last_update, last_result, last_result_date, vs_matches, overall_matches
					var elo_text = 'Rank (ELO): ' + data.datasets[tooltipItem.datasetIndex].info[tooltipItem.index][5] + " (" + data.datasets[tooltipItem.datasetIndex].info[tooltipItem.index][3] + ")";
					var exc_text = 'Elo Exchange: ' + data.datasets[tooltipItem.datasetIndex].info[tooltipItem.index][11];
					var opp_text = "vs. winrate: " + data.datasets[tooltipItem.datasetIndex].info[tooltipItem.index][2] + "% (" + data.datasets[tooltipItem.datasetIndex].info[tooltipItem.index][9] + " matches)";
					var all_text = 'overall winrate: ' + data.datasets[tooltipItem.datasetIndex].info[tooltipItem.index][1] + '% (' + data.datasets[tooltipItem.datasetIndex].info[tooltipItem.index][10] + " matches)";
					var last_text = 'last game: ' + data.datasets[tooltipItem.datasetIndex].info[tooltipItem.index][7] + ' (' + data.datasets[tooltipItem.datasetIndex].info[tooltipItem.index][8] + ')';
					var last_update = 'last update: ' + data.datasets[tooltipItem.datasetIndex].info[tooltipItem.index][6];					
					return [elo_text, exc_text, opp_text, all_text, last_text, last_update];
				}				
			}
		},
		layout: {
            padding: {
                left: 0,
                right: 0,
                top: 0,
                bottom: 0
            }
        },	
		legend: {
            display: true,
            labels: {
				padding: 10
            }
        },		
		title: {
            display: false,
            text: 'Opponent Stats',
			fontSize: 20,
			padding: 0,
        },
        scales: {
            yAxes: [{
				scaleLabel: {
					display: true,
					labelString: basebot + ' Win% vs Opponent',
					fontStyle: 'normal',
					fontSize: 16,
					padding: 0
				},
                ticks: {
                    min: -50,
                    max: 50,
					stepSize: 10,
					callback: function(value, index, values) {
                        return 50 + value + '%';
                    }
                }
            }],
            xAxes: [{
				scaleLabel: {
					display: true,
					labelString: 'Opponent Overall Win%',
					fontStyle: 'normal',
					fontSize: 16,
					padding: 0
				},				
                ticks: {
                    min: -50,
                    max: 50,
					stepSize: 10,
					callback: function(value, index, values) {
                        return 50 + value + '%';
                    }					
                }
            }]			
        }			

    }
});
</script>

<?php
}  #end of chart code.################################
?>

              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
					  <th>Author</th>
                      <th>Bot Name</th>
                      <th>Race</th>
                      <th>Games</th>
                      <th>Wins</th>
                      <th>WinPct</th>
                    </tr>
                  </thead>
                  <tbody id="items">
<?php
	$sql = "SELECT `participants`.`Name` AS Name,
			`participants`.`ID` AS ID,
			`participants`.`Race` AS Race,
			`participants`.`CurrentELO` AS ELO,
			`members`.`username` AS username,
			`members`.`Alias` AS Alias
			FROM `participants`, `members`
			WHERE `participants`.`Author` = `members`.`id`";
	$OpponentResult = $link->query($sql);
	$resultsArray = Array();
	while($opponentRow = $OpponentResult->fetch_array(MYSQLI_ASSOC))
	{
		if($opponentRow['ID'] == $_REQUEST['BotId'])
		{
			continue;
		}
		$Nextbot = new BotRecord();
		$Nextbot->botid = $opponentRow['ID'];
		$Nextbot->botname = $opponentRow['Name'];
		if(!isset($row['Alias']) || $row['Alias'] == "")
		{
			$Nextbot->author= $opponentRow['username'];
		}
		else
		{
			$Nextbot->author= $opponentRow['Alias'];
		}
		switch($opponentRow['Race'])
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
				die("Unknown race" . $opponentRow['Race']);

		}
		$sql = "SELECT COUNT(*) AS 'Matches' FROM `results` WHERE SeasonId = '" . $CurrentSeason . "' AND (Bot1 = '" . $opponentRow['ID'] . "' OR Bot2 = '" . $opponentRow['ID'] . "') AND (Bot1 = '" . mysqli_real_escape_string($link, $_REQUEST['BotId']) . "' OR Bot2 = '" . mysqli_real_escape_string($link, $_REQUEST['BotId']) . "')" ;
		$participantResult  = $link->query($sql);
		if($participantRow = $participantResult->fetch_array(MYSQLI_ASSOC))
		{
			$Nextbot->matches = $participantRow['Matches'];
		}
		else
		{
			$Nextbot->matches = 0;
		}
		$sql = "SELECT COUNT(*) AS 'Wins' FROM `results` WHERE SeasonId = '" . $CurrentSeason . "' AND `Winner` = '" . mysqli_real_escape_string($link, $_REQUEST['BotId']) . "' AND (Bot1 = '" . $opponentRow['ID'] . "' OR Bot2 = '" . $opponentRow['ID'] . "')";
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
			$Nextbot->winpct = round(( $Nextbot->wins / $Nextbot->matches) * 100, 2);
		}
		
		$resultsArray[] = $Nextbot;
	}
	usort($resultsArray, "cmp");


  foreach ($resultsArray as $Bot)
  {
	  if($Bot->matches > 0)
	  {
                  echo "<tr>
                      <td>" . htmlspecialchars($Bot->author) . "</td>
                      <td>" . htmlspecialchars($Bot->botname) . "</td>
                      <td>" . $Bot->race . "</td>
                      <td>" . $Bot->matches . "</td>
                      <td>" . $Bot->wins . "</td>
                      <td>" . number_format((float)$Bot->winpct, 2, '.', '') . "</td>
                    </tr>";
				}
  }
			  ?>
                  </tbody>
                </table>
                <hr>
                <div class="row">
                  <div class="col-md-4 col-md-offset-4 text-center">
                  	<ul class="pagination" id="myPager"></ul>
                  </div>
                </div>
              </div><!--/table-resp-->
              
              <hr>
              
             </div><!--/tab-pane-->


               

          </div><!--/tab-content-->

        </div><!--/col-9-->
    </div><!--/row-->
	</div>
	
	<?php
  require_once("footer.php");
  ?>



  </body>
</html>
                                                      