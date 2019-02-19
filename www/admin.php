<?php
session_start();
	require_once("header.php");


// Check connection
if($link->connect_error){
	die("ERROR: Could not connect. " . mysqli_connect_error());
}
$sql = "SELECT * FROM `members` WHERE `username` = '" . mysqli_real_escape_string($link, $_SESSION["username"]) . "' AND Admin='1'";
$usersResult = $link->query($sql);
if(!$usersrow = $usersResult->fetch_array(MYSQLI_ASSOC))
{
	echo "User no verified for requesting matches";
	exit();
}

$sql = "SELECT `botrequests`.`id` AS ID,
				`botrequests`.`UploadedTime` AS 'UploadedTime', 
				`botrequests`.`Comments` AS 'Comments',
                `botrequests`.`AdminComments` AS 'AdminComments',
                `botrequests`.`FileLoc` AS 'FileLoc',
                `members`.`username` AS 'Username',
                `members`.`Alias` AS 'Alias',
                `members`.`email` AS 'Email',
                `participants`.`Name` AS 'BotName'
				FROM `botrequests`, `participants`, `members`
				WHERE `botrequests`.`id` = `participants`.`ID`
				AND `participants`.`Author` = `members`.`id`
				AND `participants`.`Verified` = '0'";
				
$result = $link->query($sql);
?>	
	   <div class="container">

<div class="row">		
			<table class="table table-striped" style="width: auto;">
	<tr>
	<th>Upload Time</th>
    <th>BotName</th>
    <th>Author</th>
    <th>Email</th>
    <th>Download</th>
    <th>Comments</th>
    <th>Admin Comment</th>
	
  </tr>
 <?php

 while ($row = $result->fetch_array(MYSQLI_ASSOC))
 {
	 $AuthorName = $row["Alias"];
	 if($AuthorName == "")
	 {
		 $AuthorName = $row["Username"];
	 }
	 echo "<tr>
			<td>" . $row["UploadedTime"] . "</td>
			<td>" . $row["BotName"] . "</td>
			<td>" . $AuthorName . "</td>
			<td>" . $row["Email"] . "</td>
			<td><a href=\"" . $row['FileLoc'] . "\">Download</a></td>
			<td>" . $row["Comments"] . "</td>
			<td>" . $row["AdminComments"] . "</td>
			</tr>";
 }

 $result = $link->query($sql);
 ?>
 </div>
 </div>
 <div class="container">

<div class="row">
<div class="col-md-10 ">
<form class="form-horizontal" action="./admin.php" method="POST" enctype="multipart/form-data">

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="Real Name">Bot Name</label>  
  <div class="col-md-4">

  <div class="input-group">
       <div class="input-group-addon">
        <i class="fa fa-user">
        </i>
       </div>
	     <select class="form-control" id="BotName" Name="BotName">
<?php
	$result = $link->query($sql);
	 while ($row = $result->fetch_array(MYSQLI_ASSOC))
	{
		echo "<option value=\"" . $row["ID"] . "\">" . $row["BotName"] . "</option>";
	}
?>
  </select>
      </div>
  
    
  </div>
</div>

<!-- Checkbox input -->
<div class="form-group">
  <label class="col-md-4 control-label" for="Verified">Verified</label>  
  <div class="col-md-4">
       <div class="form-check">
       </div>
		<input id="Verified" name="Verified" class="form-check-input" type="checkbox" checked>
      </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="Admin Comments">Admin Comments</label>  
  <div class="col-md-4">
  <div class="input-group">
       <div class="input-group-addon">
        <i class="fa fa-user">
        </i>
       </div>
		<input id="AdminComment" name="AdminComment" type="text" class="form-control input-md">
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

</table>