<?php

require_once 'user.php';

session_start();
include_once 'authenticationUtilities.php';

if(!comparePermissions(array('promote'))) {
	$error = new Error(601, 'Permission Denied');
    die( json_encode($error->getInfo()) );
}

$value = NULL;
if ( isset($_GET['Username']) && !empty($_GET['Username']) ) {
    $value = $_GET['Username'];
} else {
    $error = new Error(700, "Expected Username parameter");
    die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
}

echo json_encode(getUser($value), JSON_NUMERIC_CHECK);
