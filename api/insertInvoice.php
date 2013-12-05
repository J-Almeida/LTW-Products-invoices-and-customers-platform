<?php

require_once 'invoice.php';

$jsonInvoice = NULL;
if ( isset($_POST['invoice']) && !empty($_POST['invoice']) ) {
    $jsonInvoice = $_POST['invoice'];
} else {
    $error = new Error(700, 'Missing \'invoice\' field');
    die( json_encode($error->getInfo()) );
}

// TODO select only the necessary fields from the json, return error when important fields are missing

$invoiceInfo = json_decode($jsonInvoice, true);

echo json_encode(insertInvoice($invoiceInfo), JSON_NUMERIC_CHECK);