<?php
require_once '../bootstrap.php';

require_once 'customer.php';
require_once 'authenticationUtilities.php';

if(!comparePermissions(array('write'))) {
    $error = new Error(601, 'Permission Denied');
    die( json_encode($error->getInfo()) );
}

$jsonCustomer = NULL;
if ( isset($_POST['customer']) && !empty($_POST['customer']) ) {
    $jsonCustomer = $_POST['customer'];
} else {
    $error = new Error(700, 'Missing \'customer\' field');
    die( json_encode($error->getInfo()) );
}

$customerInfo = json_decode($jsonCustomer, true);
echo json_encode(updateCustomer($customerInfo), JSON_NUMERIC_CHECK);
