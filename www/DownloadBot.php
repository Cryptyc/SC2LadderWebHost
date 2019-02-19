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
	die( "User no verified for requesting matches");
}

if(!password_verify($_REQUEST["Password"], $usersrow["password"]))
{
	die("User no verified for requesting matches " . $_REQUEST["Password"] . " " . $usersrow["password"]);
}

$sql = "SELECT * FROM `participants` WHERE `Name`='" . mysqli_real_escape_string($link, $_REQUEST["BotName"]) . "'";
$botResult =  $link->query($sql);
if(!$botRow = $botResult->fetch_array(MYSQLI_ASSOC))
{
	die("Unable to find bot " . $_REQUEST["BotName"]);
}

if(isset($_REQUEST['Data']) && $_REQUEST["Data"] == "1")
{
	$zip_location = $botRow["DataDirectory"];
}
else
{
	$zip_location = $botRow["WorkingDirectory"];
}
$attachment_location = $_SERVER["DOCUMENT_ROOT"] . "/" . $zip_location;
if (file_exists($attachment_location)) {

	header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
	header("Cache-Control: public"); // needed for internet explorer
	header("Content-Type: application/zip");
	header("Content-Transfer-Encoding: Binary");
	header("Content-Length:".filesize($attachment_location));
	header("Content-Disposition: attachment; filename=bot.zip");
	readfile($attachment_location);
	
	die();        
} else {
	die("Error: File not found.");
} 
?>