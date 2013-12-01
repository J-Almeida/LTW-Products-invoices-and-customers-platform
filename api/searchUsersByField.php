<?php
include_once 'utilities.php';
include_once 'search.php';

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