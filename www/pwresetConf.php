<?php
require 'includes/functions.php';
include_once 'config.php';


  if(isset($_REQUEST['username']) && isset($_REQUEST['email']))
  {
	 var_dump($_REQUEST);
	$link = new mysqli($host, $username, $password , $db_name);
	// Check connection
	if($link->connect_error){
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}
	$sql = "SELECT * FROM `members` WHERE `username` = '" . $_REQUEST['username'] . "' AND `email` = '" . $_REQUEST['email'] . "'";
	$result = $link->query($sql);
	if($row = $result->fetch_assoc())
	{
		$verifycode = uniqid(rand(), false);
	    $m = new MailSender;
		$m->sendMail($_REQUEST['email'], $_REQUEST['username'], $newid, 'RecoverPw');
		echo '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>You should receive an email with instructions to reset your password</div><div id="returnVal" style="display:none;">false</div>';
	}
	else
	{
		echo '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Must provide a valid email address / username combination</div><div id="returnVal" style="display:none;">false</div>';
	}
  }
  else 
  {
	echo '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Must provide a valid email address / username combination</div><div id="returnVal" style="display:none;">false</div>';
  }
?>
