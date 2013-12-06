<?php
require_once '../bootstrap.php';

require_once 'user.php';
require_once 'authenticationUtilities.php';

if(!comparePermissions(array('promote'))) {
	$error = new Error(601, 'Permission Denied');
    die( json_encode($error->getInfo()) );
}

$jsonUser = NULL;
if ( isset($_POST['user']) && !empty($_POST['user']) ) {
    $jsonUser = $_POST['user'];
} else {
    $error = new Error(700, 'Missing \'user\' field');
    die( json_encode($error->getInfo()) );
}

$userInfo = json_decode($jsonUser, true);
echo json_encode(updateUser($userInfo), JSON_NUMERIC_CHECK);
