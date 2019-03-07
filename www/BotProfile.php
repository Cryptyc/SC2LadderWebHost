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
                                                      