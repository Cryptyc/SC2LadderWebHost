<?php
//DATABASE CONNECTION VARIABLES
$host = "localhost"; // Host name. Use "db" for docker setups as it should resolve to the database container
$username = "root"; // Mysql username
$password = ""; // Mysql password
$db_name = "sc2ladder"; // Database name

//DO NOT CHANGE BELOW THIS LINE UNLESS YOU CHANGE THE NAMES OF THE MEMBERS AND LOGINATTEMPTS TABLES

$tbl_prefix = ""; //***PLANNED FEATURE, LEAVE VALUE BLANK FOR NOW*** Prefix for all database tables
$tbl_members = $tbl_prefix."members";
$tbl_attempts = $tbl_prefix."loginAttempts";
