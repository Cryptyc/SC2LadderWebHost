<?php
session_start();
require_once("dbconf.php");
$link = new mysqli($host, $username, $password , $db_name);

// Check connection
if($link->connect_error){
	die("ERROR: Could not connect. " . mysqli_connect_error());
}
$sql = "SELECT * FROM `members` WHERE `username` = '" . mysqli_real_escape_string($link, $_SESSION['username']) . "'";
$usersResult = $link->query($sql);
if(!$usersrow = $usersResult->fetch_array(MYSQLI_ASSOC))
{
	die();
}

$sql = "SELECT * FROM `participants` WHERE `Name`='" . mysqli_real_escape_string($link, $_REQUEST["BotName"]) . "' AND Author='" . $usersrow['id'] . "'";
$botResult =  $link->query($sql);
if(!$botRow = $botResult->fetch_array(MYSQLI_ASSOC))
{
	die();
}


$attachment_location = $_SERVER["DOCUMENT_ROOT"] . "/" . $botRow["DataDirectory"];
if (file_exists($attachment_location)) {

	header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
	header("Cache-Control: public"); // needed for internet explorer
	header("Content-Type: application/zip");
	header("Content-Transfer-Encoding: Binary");
	header("Content-Length:".filesize($attachment_location));
	header("Content-Disposition: attachment; filename=data.zip");
	readfile($attachment_location);
	
	die();        
} else {
	die();
} 
?>