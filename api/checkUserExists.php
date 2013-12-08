<?php
//This receives a username or an email address by post and determines if an user with that username or email already exists in the database
require_once 'authenticationUtilities.php';

$login = $_GET["Data"];

$existingUser = getUsername($login);
if( isset($existingUser) && !empty($existingUser) ) {
	echo TRUE;
	exit;
}
else {
	echo FALSE;
	exit;
}
?>