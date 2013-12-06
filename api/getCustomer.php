<?php

require_once 'customer.php';
require_once '../bootstrap.php';
require_once 'authenticationUtilities.php';

if(!comparePermissions(array('read'))) {
	$error = new Error(601, 'Permission Denied');
    die( json_encode($error->getInfo()) );
}

$value = NULL;
if ( isset($_GET['CustomerID']) && !empty($_GET['CustomerID']) ) {
    $value = $_GET['CustomerID'];
} else {
    $error = new Error(700, "Expected CustomerID parameter");
    die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
}

echo json_encode(getCustomer($value), JSON_NUMERIC_CHECK);
