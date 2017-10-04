<?php
session_start();

require_once("header.php");


if(isset($_REQUEST['uid']))
{

	$sql = "SELECT * FROM `resetrequests` WHERE `code`='" . mysqli_real_escape_string($link, $_REQUEST['uid']) . "' AND user = '" . mysqli_real_escape_string($link, $_REQUEST['u']) . "'";
	$result = $link->query($sql);
	if($row = $result->fetch_assoc())
	{

?>

    <div class="container">

      <form class="form-signup" id="pwrecover" name="pwrecover" method="post" action="pwrecover.php">
        <h2 class="form-signup-heading">Reset Password</h2>
		<?php
		echo "<input type=\"hidden\" name=\"userid\" value=\"" . $row['user'] . "\">
			<input type=\"hidden\" name=\"verifycode\" value=\"" . $_REQUEST['uid'] . "\">";
		?>
        <input name="password1" id="password1" type="password" class="form-control" placeholder="Password">
        <input name="password2" id="password2" type="password" class="form-control" placeholder="Repeat Password">

        <button name="Submit" id="submit" class="btn btn-lg btn-primary btn-block" type="submit">Reset Password</button>

        <div id="message"></div>
      </form>

    </div> <!-- /container -->

<?php
	}
	else
	{
		echo "Unable to get reset code";
	}
}
elseif (isset($_REQUEST['verifycode']) && isset($_REQUEST['password1']) && isset($_REQUEST['password2']))
{
	$sql = "SELECT * FROM `resetrequests` WHERE `code`='" . mysqli_real_escape_string($link, $_REQUEST['verifycode']) . "' AND user = '" . mysqli_real_escape_string($link, $_REQUEST['userid']) . "'";
	$result = $link->query($sql);
	if($row = $result->fetch_assoc())
	{
		if(strlen($_REQUEST['password1']) > 4 && strcmp($_REQUEST['password1'], $_REQUEST['password2']) == 0)
		{
			$newpw =  password_hash($_REQUEST['password1'], PASSWORD_DEFAULT);
			$sql = "UPDATE `members` SET `password` = '" . $newpw . "' WHERE `id` = '" . $row['user'] . "'";
			$link->query($sql);
			echo "Password reset";
		}
		else
		{
			echo "Unable to verify new password";
		}
	}
	else
	{
		echo "Unable to verify reset code";
	}
}
else 
{
		echo "Unable to verify reset code";
}	
	require_once("footer.php");
?>
    <script src="http://jqueryvalidation.org/files/dist/jquery.validate.min.js"></script>
<script src="http://jqueryvalidation.org/files/dist/additional-methods.min.js"></script>
<script>

$( "#usersignup" ).validate({
  rules: {
   password1: {
      required: true,
      minlength: 4
	},
    password2: {
      equalTo: "#password1"
    }
  }
});
</script>

  </body>
</html>
