<?php
session_start();
include_once 'utilities.php';
include_once 'search.php';
include_once 'authenticationUtilities.php';

if(!comparePermissions(array('read'))) {
    $error = new Error(601, 'Permission Denied');
    die( json_encode($error->getInfo()) );
}

$parameters = getSearchParametersFromURL();

$parameters['table'] = 'User';
$parameters['rows'] = array('username', 'name', 'email', 'permissionType');
$parameters['joins'] = array('User' => 'Permission');

$result = executeSearch($parameters);

if (!$result)
	echo '[]';
else
	echo json_encode($result, JSON_NUMERIC_CHECK);

?>