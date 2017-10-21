<?php
 
    ini_set( 'display_errors', 1 );
 
    error_reporting( E_ALL );
 
    $from = "martin@sc2ai.net";
 
    $to = "crypt9000@yahoo.com";
 
    $subject = "Checking PHP mail";
 
    $message = "PHP mail works just fine";
 
    $headers = "From:" . $from;
 
    mail($to,$subject,$message, $headers);
 
    echo "The email message was sent.";
?>