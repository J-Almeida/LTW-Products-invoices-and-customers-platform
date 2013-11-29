<?php

include 'update.php';
include 'error.php';
include 'utilities.php';

$jsonInvoice = NULL;
// TODO switch to $_POST
if ( isset($_GET['invoice']) && !empty($_GET['invoice']) ) {
    $jsonInvoice = $_GET['invoice'];
} else {
    $error = new Error(700, 'Missing \'invoice\' field');
    die( json_encode($error->getInfo()) );
}

$invoiceInfo = json_decode($jsonInvoice, true);

$table = 'Invoice';
$field = 'InvoiceNo';
$value = $invoiceInfo['invoiceNo'];
$update = new Update($table, $invoiceInfo, $field, $value);

// call getInvoice to return the updated contents
$invoiceUrl = getCurrentPageUrl();
$invoiceUrl = substr($invoiceUrl, 0, strpos($invoiceUrl, 'updateInvoice'));
$invoiceUrl .= 'getInvoice.php?InvoiceNo=';
$invoiceUrl .= urlencode($value);

$invoiceUpdated = file_get_contents($invoiceUrl);
echo $invoiceUpdated;