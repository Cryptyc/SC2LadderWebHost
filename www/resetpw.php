<?php
session_start();

require_once("header.php");

  if (isset($_SESSION['username'])) {
      session_start();
      session_destroy();
  }

	$errorcode = "";
  if(isset($_REQUEST['username']) && isset($_REQUEST['email']))
  {
	$sql = "SELECT * FROM `members` WHERE `username` = '" . mysqli_real_escape_string($link, $_REQUEST['username']) . "' AND `email` = '" . mysqli_real_escape_string($link, $_REQUEST['email']) . "'";
	$result = $link->query($sql);
	if($row = $result->fetch_assoc())
	{
		include("includes/mailsender.php");
		$verifycode = uniqid(rand(), false);
	    $m = new MailSender;
		$m->sendMail($_REQUEST['email'], $row['id'], $verifycode, 'RecoverPw');
		$sql = "INSERT INTO `resetrequests` (`user`, `code`) VALUES ('" . $row['id'] . "','" . $verifycode . "')";
		$link->query($sql);
		?>
		<div class="container">
		You should receive an email with instructions to reset your password
		</div> <!-- /container -->
<?php
	}
	else
	{
		$errorcode = "Unknown username / email combination";
	}
  }
  else
  {
?>

    <div class="container">
	<?php echo $errorcode; ?>
      <form class="form-signup" id="pwrecover" name="pwrecover" method="post" action="resetpw.php">
        <h2 class="form-signup-heading">Reset Password</h2>
        <input name="username" id="username" type="text" class="form-control" placeholder="Username" autofocus>
        <input name="email" id="email" type="text" class="form-control" placeholder="Email">
	<br>
        <button name="Submit" id="submit" class="btn btn-lg btn-primary btn-block" type="submit">Reset Password</button>

        <div id="message"></div>
      </form>

    </div> <!-- /container -->

<?php
  }
	require_once("footer.php");
?>

  </body>
</html>
