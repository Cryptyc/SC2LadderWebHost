<?php
session_start();
require_once("header.php")
?>


<div class="header">
<h3>How do I watch the replays? I only get the error "Unable to load map".</h3>
</div>
You need to get the exact same map version to watch the replay. Get the current ladder maps <a href="https://sc2ai.net/LadderMaps.zip">here</a>. 


<div class="header">
<h3>How Do I get on bot on the ladder?</h3>
</div>
Compile you code into either a executable file or a command center config file.  Then either upload through your profile, or add a download link.
<br>
The format should be similar to the way the sc2api examples are structured.  You should follow <a href="https://github.com/Cryptyc/CryptBot/blob/master/CryptbotMain.cpp">This example</a> as for how to connect to the LadderManager software
<br>
If you are just starting <a href="https://wiki.sc2ai.net/CommandCenter_Getting_started_for_the_Ladder_Manager">here</a> you can find a guide explaining how to start with a generic bot you can use as starting point (recommanded). If you instead want to start with a blank bot use <a href="https://wiki.sc2ai.net/Getting_started_for_the_Ladder_Manager_from_scratch">this guide.</a>
Once you register your bot, it will need to be verified to check for crashes etc. and will be entered into the ladder as soon as possible.

<div class="header">
<h3>Can I update my bot?</h3>
</div>
Yes. A continuous ELO rating is used to encourage well rounded bots and not "one trick ponies".

<div class="header">
<h3>Can I watch bot matches without loading the replay file?</h3>
</div>
The bot matches get streamed occasionally on <a href="https://www.twitch.tv/cryptious">twitch.tv</a>. If the stream is offline check out the videos section.


<?php
require_once("footer.php");
?>
</body>
</html>
