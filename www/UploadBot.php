<?php

require_once("dbconf.php");
$link = new mysqli($host, $username, $password , $db_name);

// Check connection
if($link->connect_error){
	die("ERROR: Could not connect. " . mysqli_connect_error());
}
$sql = "SELECT * FROM `members` WHERE `username` = '" . mysqli_real_escape_string($link, $_REQUEST["Username"]) . "'";
$usersResult = $link->query($sql);
if(!$usersrow = $usersResult->fetch_array(MYSQLI_ASSOC))
{
	$jsonOut["result"] = false;
	$jsonOut["error"] = "User not verified for requesting matches";
}

if(!password_verify($_REQUEST["Password"], $usersrow["password"]))
{
	$jsonOut["result"] = false;
	$jsonOut["error"] = "User not verified for requesting matches";
	echo json_encode($jsonOut);
	exit();
}

$sql = "SELECT * FROM `participants` WHERE `Name`='" . mysqli_real_escape_string($link, $_REQUEST["BotName"]) . "'";
$botResult =  $link->query($sql);
if(!$botRow = $botResult->fetch_array(MYSQLI_ASSOC))
{
	$jsonOut["result"] = false;
	$jsonOut["error"] = "Unable to find bot " . $_REQUEST["BotName"];
	echo json_encode($jsonOut);
	exit();
}

if(!isset($_REQUEST["Checksum"]))
{
	$jsonOut["result"] = false;
	$jsonOut["error"] = "no checksum found";
	echo json_encode($jsonOut);
	exit();
}

if(isset($_FILES['BotFile']))
{
	if(isset($_REQUEST['Data']) && $_REQUEST["Data"] == 1)
	{
		$dbField = "DataDirectory";
		$zip_location = "DataDirs/" . $_REQUEST["BotName"] . ".zip";
	}
	else
	{
		$dbField = "WorkingDirectory";
		$zip_location = "workingdirs/" . $_REQUEST["BotName"] . ".zip";
	}
	$attachment_location = $_SERVER["DOCUMENT_ROOT"] . '/' .  $zip_location;
	$tmp_name = $_FILES["BotFile"]["tmp_name"];
	$checksum = md5_file($_FILES["BotFile"]["tmp_name"]);
	if($checksum != $_REQUEST["Checksum"])
	{
		$jsonOut["result"] = false;
		$jsonOut["error"] = "Checksum not valid: Uploaded: " . $checksum . " original: " . $_REQUEST["Checksum"];
		echo json_encode($jsonOut);
		exit();
	}
	unlink($attachment_location);
	move_uploaded_file($tmp_name, $attachment_location);
	$sql = "UPDATE `participants` SET `" . $dbField . "`='" . mysqli_real_escape_string($link, $zip_location) . "' WHERE `Name`='" . mysqli_real_escape_string($link, $_REQUEST["BotName"]) . "'";
	$result = $link->query($sql);
	$jsonOut["result"] = true;
	$jsonOut["error"] = "Success";
	echo json_encode($jsonOut);
	exit();
}

?>