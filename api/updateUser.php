<?php
session_start();

include_once 'error.php';
include_once 'utilities.php';
include_once 'update.php';
include_once 'insert.php';
include_once 'authenticationUtilities.php';

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

// TODO select only the necessary fields from the json, return error when important fields are missing

$table = 'User';
$field = 'username';
$username = $userInfo['username'];
$update = new Update($table, $userInfo, $field, $username);

// call getUser to return the updated contents
$userUrl = getAPIUrl('User', 'Username', $username);
$userUpdated = file_get_contents($userUrl);
echo $userUpdated;
