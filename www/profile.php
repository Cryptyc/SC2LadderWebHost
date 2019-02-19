<?php
session_start();

require_once("header.php");
require_once("ImageCheck.php");

$sql = "SELECT * FROM `members` WHERE `username` = '" . mysqli_real_escape_string($link, $_SESSION['username']) . "'";
	$result = $link->query($sql);
	if(!$row = $result->fetch_assoc())
	{
		die("unable to get profile");
	}
	$pwError = "";
	if(isset($_REQUEST['submit']))
	{
		
		$sql = " UPDATE `members` SET `Alias` = '" .  mysqli_real_escape_string($link,$_REQUEST['Alias']) . "', `Name` = '" .  mysqli_real_escape_string($link,$_REQUEST['RealName']) . "', `Website` = '" .  mysqli_real_escape_string($link,$_REQUEST['Website']) . "', `Github` = '" .  mysqli_real_escape_string($link,$_REQUEST['Github']) . "' WHERE `members`.`username` = '" . mysqli_real_escape_string($link,$_SESSION['username']) . "'";
		$result = $link->query($sql);
		if(isset($_REQUEST['Password1']) || isset($_REQUEST['Password2']))
		{
			if(strlen($_REQUEST['Password1']) > 4 && strcmp($_REQUEST['Password1'], $_REQUEST['Password2']) == 0)
			{
				$newpw =  password_hash($_REQUEST['Password1'], PASSWORD_DEFAULT);
				$sql = "UPDATE `members` SET `password` = '" . $newpw . "' WHERE `username` = '" . mysqli_real_escape_string($link,$_SESSION['username']) . "'";
				$link->query($sql);
				$pwError = "<div class=\"alert alert-success alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Password successfully changed</div>";
			}
			else 
			{
				$pwError = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Unable to reset password.  Passwords didn't match or doesn't match strength requirements</div>";
			}
		}

	


	if(isset($_FILES['Avatar']) && $_FILES['Avatar']['size'] > 2)
	{
		var_dump($_FILES);
		$user_path = "./user_images/" . $row['id'];
		if (!file_exists($user_path)) {
			mkdir($user_path, 0777, true);
		}
		$out_user = uploadFile("Avatar", $user_path . "/");
		$out_filename = $out_user['filepath'] . $out_user['filename'];
		$sql = " UPDATE `members` SET `Avatar` = '" .  mysqli_real_escape_string($link, $out_filename) . "' WHERE `members`.`username` = '" . $_SESSION['username'] . "'";
		$result = $link->query($sql);
	}
	$sql = "SELECT * FROM `members` WHERE `username` = '" . mysqli_real_escape_string($link, $_SESSION['username']) . "'";
	$result = $link->query($sql);
	if(!$row = $result->fetch_assoc())
	{
		die("unable to get profile");
	}

	}
?>

   <div class="container">
<div class="row">
<div class="col-md-10 ">
<form class="form-horizontal" action="./profile.php" method="POST" enctype="multipart/form-data">
<fieldset>

<!-- Form Name -->
<legend>Author profile</legend>

<!-- Text input-->


<div class="form-group">
  <label class="col-md-4 control-label" for="Alias">Alias</label>  
  <div class="col-md-4">
 <div class="input-group">
       <div class="input-group-addon">
        <i class="fa fa-user">
        </i>
       </div>
	   <?php
       echo "<input id=\"Alias\" name=\"Alias\" type=\"text\" value=\"" . $row['Alias'] . "\" class=\"form-control input-md\">";
	   ?>
      </div>

    
  </div>

  
</div>

<!-- File Button --> 
<div class="form-group">
  <label class="col-md-4 control-label" for="Upload photo">Avatar</label>
  <div class="col-md-4">
    <input id="Avatar" name="Avatar" class="input-file" type="file">
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="Real Name">Real Name</label>  
  <div class="col-md-4">

  <div class="input-group">
       <div class="input-group-addon">
        <i class="fa fa-user">
        </i>
       </div>
	   <?php
       echo "<input id=\"RealName\" name=\"RealName\" type=\"text\" value=\"" . $row['Name'] . "\" class=\"form-control input-md\">";
	   ?>
      </div>
  
    
  </div>
</div>


<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="Github Link">Website Link</label>  
  <div class="col-md-4">
  <div class="input-group">
       <div class="input-group-addon">
        <i class="fa fa-user">
        </i>
       </div>
	   <?php
       echo "<input id=\"Website\" name=\"Website\" type=\"text\" value=\"" . $row['Website'] . "\" class=\"form-control input-md\">";
	   ?>
      </div>
    
  </div>
</div>

<div class="form-group">
  <label class="col-md-4 control-label" for="Github Link">Github Link</label>  
  <div class="col-md-4">
  <div class="input-group">
       <div class="input-group-addon">
        <i class="fa fa-user">
        </i>
       </div>
   	   <?php
       echo "<input id=\"Github\" name=\"Github\" type=\"text\" value=\"" . $row['Github'] . "\" class=\"form-control input-md\">";
	   ?>

      </div>
    
  </div>
</div>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="Real Name">New Password</label>  
  <div class="col-md-4">

  <div class="input-group">
       <div class="input-group-addon">
        <i class="fa fa-user">
        </i>
       </div>
	   <input id="Password1" name="Password1" type="password" value="" class="form-control input-md">
      </div>
  
    
  </div>
</div>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="Real Name">Confirm password</label>  
  <div class="col-md-4">

  <div class="input-group">
       <div class="input-group-addon">
        <i class="fa fa-user">
        </i>
       </div>
	  <input id="Password2" name="Password2" type="password" value="" class="form-control input-md">
	  
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
  <?php echo $pwError; ?>

</div>
<div class="col-md-2 hidden-xs">
<?php
echo "<img src=\"" . $row['Avatar'] . "\" class=\"img-responsive img-thumbnail \">";
?>  </div>


</div>
   </div>
<?php
  require_once("footer.php");
  ?>



  </body>
</html>
