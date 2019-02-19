<?php
	session_start();
	$page = "news";

	require_once("header.php");

	$sql = "SELECT * FROM `members` WHERE `ProfileVisible` = '1'";

	$result = $link->query($sql);

	?>
<iframe
    src="http://player.twitch.tv/?channel=starcraft2ai"
    height="600"
    width="800"
    frameborder="0"
    scrolling="no"
    allowfullscreen="true">
</iframe>


<?php
	require_once("footer.php");
?>
</body>
</html>