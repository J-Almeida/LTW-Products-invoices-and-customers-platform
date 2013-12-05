<?php
session_start();

require_once 'product.php';

if(!isset($_SESSION['username']) || !isset($_SESSION['permissions']) || $_SESSION['permissions']['write'] != '1') {
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
echo json_encode(updateProduct($productInfo), JSON_NUMERIC_CHECK);