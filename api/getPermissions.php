<?php
include_once 'search.php';

function getAllPermissions() {
	$parameters = array('field' => "", 'values' => "", 'operation' => "listall");
	$parameters['table'] = 'Permission';
	$parameters['rows'] = array('permissionId', 'permissionType');
	$parameters['joins'] = array();

	$result = executeSearch($parameters);
	return $result;
}

session_start(); 
if(isset($_SESSION['username']) && isset($_SESSION['permissions']) && !empty($_SESSION['permissions'])) {
	echo json_encode($_SESSION['permissions']);
}
else
	echo "none";
?>