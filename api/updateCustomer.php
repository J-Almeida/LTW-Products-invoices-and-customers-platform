<?php

include_once 'error.php';
include_once 'utilities.php';
include_once 'update.php';
include_once 'insert.php';

$jsonCustomer = NULL;
if ( isset($_POST['customer']) && !empty($_POST['customer']) ) {
    $jsonCustomer = $_POST['customer'];
} else {
    $error = new Error(700, 'Missing \'customer\' field');
    die( json_encode($error->getInfo()) );
}

$customerInfo = json_decode($jsonCustomer, true);

// TODO select only the necessary fields from the json, return error when important fields are missing

$table = 'Customer';
$field = 'customerId';
$customerId = $customerInfo['customerId'];
if ($customerId == NULL) {
    $insert = new Insert('Customer', $customerInfo);
    $customerId = getId('Customer', 'customerTaxId', $customerInfo['customerTaxId']);
} else
    $update = new Update($table, $customerInfo, $field, $customerId);

// call getCustomer to return the updated contents
$customerUrl = getAPIUrl('Customer', 'CustomerID', $customerId);
$customerUpdated = file_get_contents($customerUrl);
echo $customerUpdated;
