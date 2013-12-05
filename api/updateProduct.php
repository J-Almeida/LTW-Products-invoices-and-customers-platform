<?php
session_start();

include_once 'error.php';
include_once 'utilities.php';
include_once 'update.php';
include_once 'insert.php';
include_once 'authenticationUtilities.php';

if(!comparePermissions(array('write'))) {
    $error = new Error(601, 'Permission Denied');
    die( json_encode($error->getInfo()) );
}

$jsonProduct = NULL;
if ( isset($_POST['product']) && !empty($_POST['product']) ) {
    $jsonProduct = $_POST['product'];
} else {
    $error = new Error(700, 'Missing \'product\' field');
    die( json_encode($error->getInfo()) );
}

$productInfo = json_decode($jsonProduct, true);

// TODO select only the necessary fields from the json, return error when important fields are missing

$table = 'Product';
$field = 'productCode';
$productCode = $productInfo['productCode'];
if ($productCode == NULL) {
    $productCode = getLastProductCode() + 1;
    $productInfo['productCode'] = $productCode;
    $insert = new Insert('Product', $productInfo);
} else
    $update = new Update($table, $productInfo, $field, $productCode);

// call getProduct to return the updated contents
$productUrl = getAPIUrl('Product', 'ProductCode', $productCode);
$productUpdated = file_get_contents($productUrl);
echo $productUpdated;

function getLastProductCode(){
    $table = 'Product';
    $field = 'productCode';
    $values = array();
    $rows = array('productCode');
    $max = new MaxSearch($table, $field, $values, $rows);
    return $max->getResults()[0]['productCode'];
}