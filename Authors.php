<?php
	session_start();
	require_once("header.php");

	$sql = "SELECT * FROM `members` WHERE `ProfileVisible` = '1'";

	$result = $link->query($sql);

	?>
	
    <div class="container">
	<?php
	$curRow = 0;
	while($row = $result->fetch_assoc())
	{
		$sql = "SELECT * FROM `participants` WHERE `Author` = '" . $row['id'] . "'";
		$aresult = $link->query($sql);
		if($arow = $aresult->fetch_assoc())
		{
		if($curRow == 0)
		{
			echo "<div class=\"row\">";
		}
		echo "<div class=\"col-md-3\">";
		if ($row['Avatar'] == "")
		{
			echo "<img class=\"img-thumbnail\" src=\"./images/avatar.jpg\">";
		}
		else
		{
			echo "<img class=\"img-thumbnail\" src=\"" . $row['Avatar'] . "\">";
		}
		?>
                    <div class="media-body text-center">
					
                        <h3><?php 
						if($row['Alias'] == "")
						{
							echo $row['username'];
						}
						else 
						{
							echo $row['Alias']; 
						}
						?></h3>
                        <p><a <?php echo "href=\"AuthorProfile.php?author=" . $row['id'] . "\""; ?> name="View Profile" id="profile" class="btn btn-lg btn-primary btn-block" type="submit">View Profile</a>
						</p>
                    </div>
				</div>
			<?php
			$curRow ++;
			if($curRow > 2)
			{
				echo "</div>";
				$curRow = 0;
			}
		}
	}
			?>
         
    </div>

<?php
	require_once("footer.php");
?>
</body>
</html>