<?php
session_start();

function CheckBotOwner($BotId, $Link)
{
	$sql = "SELECT `members`.`id` FROM `members`, `participants` WHERE `members`.`id` = `participants`.`Author` AND `participants`.`id` = '" . $BotId . "' AND `members`.`username` = '" . $_SESSION['username'] . "'";
	$result = $Link->query($sql);
	if($row = $result->fetch_array(MYSQLI_ASSOC))
	{
		return true;
	}
	return false;
}

require_once("header.php");

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
		$sql = "SELECT `id` FROM `members` WHERE `username` = '" . mysqli_real_escape_string($link, $_SESSION['username']) . "'";
		$result = $link->query($sql);
		if($row = $result->fetch_array(MYSQLI_ASSOC))
		{
			$location = "";
			if(isset($_FILES['FileUpload']))
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
			}
			$sql = "INSERT INTO `participants` (`Name`, `Author`, `Race`, `EloFormat`) VALUES ('" . mysqli_real_escape_string($link, $_REQUEST['BotName']) . "', '" . $row['id'] . "', '" . GetRaceId($_REQUEST['Race']) . "', '1')";
			$result = $link->query($sql);
			$BotID = $link->insert_id;
			$sql = "INSERT INTO `botrequests` (`id`, `FileLoc`, `DownloadLink`, `Comments`) VALUES ('" . $BotID . "', '" . $location . "', '" . mysqli_real_escape_string($link, $_REQUEST['Download']) . "','" . mysqli_real_escape_string($link, $_REQUEST['Comments']) . "')";
			$result = $link->query($sql);
			

		}
		else
		{
			die("unable to fine profile");
		}
				
	}

	$sql = "SELECT `participants`.`Name` AS Name, 
			`participants`.`ID` AS ID,
			`participants`.`Race` AS Race,
			`participants`.`Verified` AS Verified,
			`participants`.`Deactivated` AS Deactivated
			FROM `participants`, `members`
			WHERE `participants`.`Author` = `members`.`id`
			AND `members`.`username` = '" . mysqli_real_escape_string($link, $_SESSION['username']) . "'
			AND `participants`.`Deleted` = '0'";
			
	$result = $link->query($sql);
?>
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script type="text/javascript" src="js/bootstrap.js"></script>

    <script type="text/javascript" src="js/bootbox.min.js"></script>
    <script type="text/javascript" src="js/bootbox.confirm.js"></script>

                       <div class="header">
						<h3> My Bots</h3>
                        </div>
			<table class="table table-striped" style="width: auto;">
	<tr>
    <th>BotName</th>
    <th>Race</th>
    <th>Status</th>
    <th></th>
    <th></th>
	
  </tr>
 <?php
 while ($row = $result->fetch_array(MYSQLI_ASSOC))
 {
	 echo "<tr>
				<td>" . $row['Name'] . "</td>
				<td>" . GetRace($row['Race']) . "</td>
				<td>";
	if($row['Verified'] == 0)
	{
		echo "Awaiting Verification
			</td>
			<td>
			</td>";
	}
	else if ($row['Deactivated'] == 0)
	{
		echo "Active </td>
				<td>
			<a href=\"Bots.php?DeactivateBotId=" . $row['ID'] . "\" class=\"btn btn-success\" >Deactivate</a>
			</td>";
	}
	else
	{
		echo "Deactivated </td>
				<td>
			<a href=\"Bots.php?ActivateBotId=" . $row['ID'] . "\" class=\"btn btn-warning\" >Activate</a>
			</td>";
		
	}
	echo "</td>
			<td>
			<a class=\"btn btn-danger\" data-toggle=\"confirmation\" data-title=\"Really Delete bot?\"   OnClick=\"DeleteConfirm(" .$row['ID'] . ")\">Delete</a>
			</td>";
 }
?>
</table>
                       <div class="header">
						<h3>Add new bot</h3>
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
		<input id="BotName" name="BotName" type="text" class=\"form-control input-md\">";
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
