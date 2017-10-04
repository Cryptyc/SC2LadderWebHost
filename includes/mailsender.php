<?php
class MailSender
{
    public function sendMail($email, $user, $id, $type)
    {
        include 'config.php';

        $finishedtext = $active_email;

			

        //Sets message body content based on type (verification or confirmation)
        if ($type == 'Verify') {
 
			// ADD $_SERVER['SERVER_PORT'] TO $verifyurl STRING AFTER $_SERVER['SERVER_NAME'] FOR DEV URLS USING PORTS OTHER THAN 80
			// substr() trims "createuser.php" off of the current URL and replaces with verifyuser.php
			// Can pass 1 (verified) or 0 (unverified/blocked) into url for "v" parameter
			$verifyurl = $base_url .  "verifyuser.php?v=1&uid=" . $id;
			$from = "noreply@sc2ai.net";
			$to = $email;
 			$subject = $user . ' Account Verification';
 			$message = $verifymsg . '<br><a href="'.$verifyurl.'">'.$verifyurl.'</a>';
 
			$headers = "From:" . $from;
			mail($to,$subject,$message, $headers);
 

        } elseif ($type == 'Active') {

 
			$from = "noreply@sc2ai.net";
			$to = $email;
 			$subject = $site_name . ' Account Created!';
 			$message = $active_email . '<br><a href="'.$signin_url.'">'.$signin_url.'</a>';
 
			$headers = "From:" . $from;
			mail($to,$subject,$message, $headers);


        }
		elseif ($type == 'RecoverPw')
		{
			// ADD $_SERVER['SERVER_PORT'] TO $verifyurl STRING AFTER $_SERVER['SERVER_NAME'] FOR DEV URLS USING PORTS OTHER THAN 80
			// substr() trims "createuser.php" off of the current URL and replaces with verifyuser.php
			// Can pass 1 (verified) or 0 (unverified/blocked) into url for "v" parameter
			$verifyurl = $base_url .  "pwrecover.php?v=1&uid=" . $id . "&u=" . $user;

			$from = "noreply@sc2ai.net";
			$to = $email;
 			$subject = $site_name . ' Password recovery';
 			$message = $password_recover . '<br><a href="'.$verifyurl.'">'.$verifyurl.'</a>';
 
			$headers = "From:" . $from;
			mail($to,$subject,$message, $headers);
		};

    }
}
