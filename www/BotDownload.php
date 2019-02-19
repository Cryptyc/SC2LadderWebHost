<?php
session_start();
require_once("dbconf.php");
$link = new mysqli($host, $username, $password , $db_name);

// Check connection
if($link->connect_error){
	die("ERROR: Could not connect. " . mysqli_connect_error());
}
$sql = "SELECT `botrequests`.`id` AS ID,
                `botrequests`.`FileLoc` AS 'FileLoc',
                `participants`.`Downloadable` AS 'Downloadable'
				FROM `botrequests`, `participants`
				WHERE `botrequests`.`id` = `participants`.`ID`
				AND `participants`.`Name` = '" . mysqli_real_escape_string($link, $_REQUEST['BotName']) . "'";
$result = $link->query($sql);
if($botRow = $result->fetch_array(MYSQLI_ASSOC))
{
	$attachment_location = $_SERVER["DOCUMENT_ROOT"] . "/" . $botRow["FileLoc"];
	if (file_exists($attachment_location) && $botRow["Downloadable"] == 1) {

		header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
		header("Cache-Control: public"); // needed for internet explorer
		header("Content-Type: application/zip");
		header("Content-Transfer-Encoding: Binary");
		header("Content-Length:".filesize($attachment_location));
		header("Content-Disposition: attachment; filename=" .$_REQUEST['BotName'] . ".zip");
		readfile($attachment_location);
	
		die();        
	}
} 
	die();

?>