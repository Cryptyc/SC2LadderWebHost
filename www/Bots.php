<?php
session_start();

function CheckBotOwner($BotId, $Link)
{
	$sql = "SELECT `members`.`id`, `members`.`AutoAuth` FROM `members`, `participants` WHERE `members`.`id` = `participants`.`Author` AND `participants`.`id` = '" . $BotId . "' AND `members`.`username` = '" . $_SESSION['username'] . "'";
	$result = $Link->query($sql);
	if($row = $result->fetch_array(MYSQLI_ASSOC))
	{
		return true;
	}
	return false;
}

require_once("header.php");
	$errorText = "";
	if(!isset($_SESSION['username']))
	{
		die("ERROR: invalid request");
	}
	if(isset($_REQUEST['ActivateBotId']))
	{
		if(CheckBotOwner($_REQUEST['ActivateBotId'], $link))
		{
			$sql = "UPDATE `participants` SET `Deactivated` = '0' WHERE `ID` = '" . mysqli_real_escape_string($link, $_REQUEST['ActivateBotId']) . "'";
			$result = $link->query($sql);
		}
	}
	if(isset($_REQUEST['PublicBotId']))
	{
		if(CheckBotOwner($_REQUEST['PublicBotId'], $link))
		{
			$sql = "UPDATE `participants` SET `Downloadable` = '1' WHERE `ID` = '" . mysqli_real_escape_string($link, $_REQUEST['PublicBotId']) . "'";
			$result = $link->query($sql);
		}
	}
	if(isset($_REQUEST['PrivateBotId']))
	{
		if(CheckBotOwner($_REQUEST['PrivateBotId'], $link))
		{
			$sql = "UPDATE `participants` SET `Downloadable` = '0' WHERE `ID` = '" . mysqli_real_escape_string($link, $_REQUEST['PrivateBotId']) . "'";
			$result = $link->query($sql);
		}
	}
	if(isset($_REQUEST['DeactivateBotId']))
	{
		if(CheckBotOwner($_REQUEST['DeactivateBotId'], $link))
		{
			$sql = "UPDATE `participants` SET `Deactivated` = '1' WHERE `ID` = '" . mysqli_real_escape_string($link, $_REQUEST['DeactivateBotId']) . "'";
			$result = $link->query($sql);
		}
	}
	if(isset($_REQUEST['DeleteBotId']))
	{
		if(CheckBotOwner($_REQUEST['DeleteBotId'], $link))
		{
			$sql = "UPDATE `participants` SET `Deleted` = '1' WHERE `ID` = '" . mysqli_real_escape_string($link, $_REQUEST['DeleteBotId']) . "'";
			$result = $link->query($sql);
		}
	}
	if(isset($_REQUEST['submit']) && isset($_REQUEST['BotName']))
	{
		$sql = "SELECT `id`, `AutoAuth` FROM `members` WHERE `username` = '" . mysqli_real_escape_string($link, $_SESSION['username']) . "'";
		$result = $link->query($sql);
		if($row = $result->fetch_array(MYSQLI_ASSOC))
		{
			$location = "";
			if(isset($_FILES['FileUpload']))
			{
				$allowed =  array('zip');
				$filename = $_FILES['FileUpload']['name'];
				$ext = pathinfo($filename, PATHINFO_EXTENSION);
				if(in_array($ext,$allowed) ) 
				{
					$file_name= $_FILES['FileUpload']['name'];
					$file_name = preg_replace("/[^a-zA-Z0-9._-]/", "", $file_name);
					$location = "./uploads/" . $row['id'];
					if (!file_exists($location)) {
						mkdir($location, 0777, true);
					}
					$location .= "/" . $file_name;
					move_uploaded_file($_FILES["FileUpload"]["tmp_name"], $location);
				}
				else
				{
					$errorText =  "Only zip file uploads are supported";
				}
			}
			else
			{
			}
			$sql = "SELECT * FROM `participants` WHERE `Author` = '" .  $row['id'] . "' AND `Name` = '" . mysqli_real_escape_string($link, $_REQUEST['BotName']) . "' AND `Race` = '" . GetRaceId($_REQUEST['Race']) ."'";
			$Botresult = $link->query($sql);
			$BotID = 0;
			if($BotRow = $Botresult->fetch_array(MYSQLI_ASSOC))
			{
				$sql = "UPDATE `participants` SET `Deleted`='0', `Verified` = '0' WHERE `ID` = '" . $BotRow['ID'] . "'";
				$result = $link->query($sql);
				$BotID = $BotRow['ID'];
				$sql = "UPDATE `botrequests` SET `UploadedTime`= NOW(),`FileLoc` ='" . $location . "', `DownloadLink`='" . mysqli_real_escape_string($link, $_REQUEST['Download']) . "', `Comments` = '" . mysqli_real_escape_string($link, $_REQUEST['Comments']) . "' WHERE `id`='" . $BotID . "'";
				$result = $link->query($sql);

			}
			else
			{
				$sql = "SELECT * FROM `participants` WHERE `Name` = '" . mysqli_real_escape_string($link, $_REQUEST['BotName']) . "'";
				$Botresult = $link->query($sql);
				if($BotRow = $Botresult->fetch_array(MYSQLI_ASSOC))
				{
					$errorText =  "That bot name is already taken";
				}
				else
				{
					$downloadable = 0;
					if($_REQUEST['Downloadable'])
					{
						$downloadable = 1;
					}
					$sql = "INSERT INTO `participants` (`Name`, `Author`, `Race`, `EloFormat`, `Downloadable`) VALUES ('" . mysqli_real_escape_string($link, $_REQUEST['BotName']) . "', '" . $row['id'] . "', '" . GetRaceId($_REQUEST['Race']) . "', '1', '" . $downloadable . "')";
					$result = $link->query($sql);
					$BotID = $link->insert_id;
					$sql = "INSERT INTO `botrequests` (`id`, `FileLoc`, `DownloadLink`, `Comments`) VALUES ('" . $BotID . "', '" . $location . "', '" . mysqli_real_escape_string($link, $_REQUEST['Download']) . "','" . mysqli_real_escape_string($link, $_REQUEST['Comments']) . "')";
					$result = $link->query($sql);
				}
			}
			
			if($row['AutoAuth'] == 1 && $BotID > 0)
			{
				$botname = preg_replace("/[^a-zA-Z0-9._-]/", "", $_REQUEST['BotName']);
				$dest = "./workingdirs/" . $botname . ".zip";
//				echo "moving " . $location . " to " . $dest;
				if(copy($location, $dest))
				{
					$sql = "UPDATE `participants` SET `verified` ='1', `WorkingDirectory` = '" . $dest . "' WHERE `ID` = '" . $BotRow['ID'] . "'";
					$result = $link->query($sql);
				}
			}
			else
			{
//				echo "not in auto auth list " . $row['AutoAuth'] . " BotId " . $BotID;
			}
		}
		else
		{
			die("unable to find profile");
		}
				
	}

	$sql = "SELECT `participants`.`Name` AS Name, 
			`participants`.`ID` AS ID,
			`participants`.`Race` AS Race,
			`participants`.`Verified` AS Verified,
			`participants`.`Deactivated` AS Deactivated,
			`participants`.`Downloadable` AS Downloadable,
			`participants`.`EloFormat` AS EloFormat
			FROM `participants`, `members`
			WHERE `participants`.`Author` = `members`.`id`
			AND `members`.`username` = '" . mysqli_real_escape_string($link, $_SESSION['username']) . "'
			AND `participants`.`Deleted` = '0'";
			
	$result = $link->query($sql);
?>
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script type="text/javascript" src="js/bootstrap.min.js"></script>

    <script type="text/javascript" src="js/bootbox.min.js"></script>
    <script type="text/javascript" src="js/bootbox.confirm.js"></script>

                       <div class="header">
						<h3> My Bots</h3>
                        </div>
						<?php
						if($errorText != "")
						{
							echo "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>" . $errorText . "</div>";
						}
						?>
			<table class="table table-striped" style="width: auto;">
	<tr>
    <th>BotName</th>
    <th>Race</th>
    <th>Status</th>
    <th>Distribution</th>
    <th></th>
    <th></th>
	<th></th>
	
  </tr>
 <?php
 while ($row = $result->fetch_array(MYSQLI_ASSOC))
 {
	 $downloadableText = "";
	if($row['Downloadable'])
	{
		$downloadableText = "Public";
	}
	else
	{
		$downloadableText = "Private";
	}
	 echo "<tr>
				<td>" . htmlspecialchars($row['Name']) . "</td>
				<td>" . GetRace($row['Race']) . "</td>
				<td>";
	if($row['Verified'] == 0)
	{
		echo "Awaiting Verification
			</td>
			<td>
			</td><td></td>";
	}
	else if($row['EloFormat'] == 0)
	{
		echo "Format invalid
			</td>
			<td>
			</td><td></td>";
	}
	else if ($row['Deactivated'] == 0)
	{
		echo "Active </td>
				<td>
				" . $downloadableText . "</td><td>
			<a href=\"Bots.php?DeactivateBotId=" . $row['ID'] . "\" class=\"btn btn-success\" >Deactivate</a>
			</td>";
	}
	else
	{
		echo "Deactivated </td>
				<td>
				" . $downloadableText . "</td><td>
			<a href=\"Bots.php?ActivateBotId=" . $row['ID'] . "\" class=\"btn btn-warning\" >Activate</a>
			</td>";
		
	}
	if($row['Downloadable'])
	{
		echo "<td>
			<a href=\"Bots.php?PrivateBotId=" . $row['ID'] . "\" class=\"btn btn-warning\" >Make Private</a>
			</td>";
	}
	else
	{
		echo "<td>
			<a href=\"Bots.php?PublicBotId=" . $row['ID'] . "\" class=\"btn btn-success\" >Make Public</a>
			</td>";
	}
	echo "<td>
			<a class=\"btn btn-danger\" data-toggle=\"confirmation\" data-title=\"Really Delete bot?\"   OnClick=\"DeleteConfirm(" .$row['ID'] . ")\">Delete</a>
			</td>";
 }
?>
</table>
                       <div class="header">
						<h3>Add/Edit bot</h3>
						<h4>Please follow our <a href="http://wiki.sc2ai.net/Bot_Upload_Checklist">Bot Upload Checklist</a></h4>
						<h4>To edit current bot, enter the same name</h4>
                        </div>
	   <div class="container">
<div class="row">
<div class="col-md-10 ">
<form class="form-horizontal" action="./Bots.php" method="POST" enctype="multipart/form-data">

						<div class="form-group">
  <label class="col-md-4 control-label" for="BotName">Bot Name</label>  
  <div class="col-md-4">
 <div class="input-group">
       <div class="input-group-addon">
        <i class="fa fa-user">
        </i>
       </div>
		<input id="BotName" name="BotName" type="text" class=\"form-control input-md\">
      </div>
  </div>

  
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="Real Name">Race</label>  
  <div class="col-md-4">

  <div class="input-group">
       <div class="input-group-addon">
        <i class="fa fa-user">
        </i>
       </div>
	     <select class="form-control" id="Race" Name="Race">
    <option>Terran</option>
    <option>Zerg</option>
    <option>Protoss</option>
    <option>Random</option>
  </select>
      </div>
  
    
  </div>
</div>
<!-- File Button --> 
<div class="form-group">
  <label class="col-md-4 control-label" for="FileUpload">Bot File</label>
  <div class="col-md-4">
     <input id="FileUpload" name="FileUpload" class="input-file" type="file">
  </div>
</div>

<!-- Checkbox input -->
<div class="form-group">
  <label class="col-md-4 control-label" for="Downloadable">Publicly Downloadable</label>  
  <div class="col-md-4">
       <div class="form-check">
       </div>
		<input id="Downloadable" name="Downloadable" class="form-check-input" type="checkbox" checked>
      </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="Download Link">Download Link</label>  
  <div class="col-md-4">
  <div class="input-group">
       <div class="input-group-addon">
        <i class="fa fa-user">
        </i>
       </div>
		<input id="Download" name="Download" type="text" class="form-control input-md">
      </div>
    
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="Github Link">Additional Info</label>  
  <div class="col-md-4">
  <div class="input-group">
       <div class="input-group-addon">
      <i class="fa fa-male" style="font-size: 20px;"></i>
        
       </div>
	   <textarea class="form-control" rows="5" id="Comments" name="Comments"></textarea>

      </div>
    
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label" ></label>  
  <div class="col-md-4">
  <input type="submit" class="btn btn-success" name="submit" value="submit">
    
  </div>
</div>
</fieldset>
</form>
</div>
</div>
</div>
  </body>
</html>
