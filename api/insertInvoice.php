<?php

include_once 'insert.php';
include_once 'error.php';
include_once 'utilities.php';

$jsonInvoice = NULL;
if ( isset($_POST['invoice']) && !empty($_POST['invoice']) ) {
    $jsonInvoice = $_POST['invoice'];
} else {
    $error = new Error(700, 'Missing \'invoice\' field');
    die( json_encode($error->getInfo()) );
}

// TODO select only the necessary fields from the json, return error when important fields are missing

$invoiceInfo = json_decode($jsonInvoice, true);

$table = 'Invoice';
// INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
//              VALUES ("FT SEQ/1", "2013-09-27", 1, 1);
$invoiceLines = $invoiceInfo['line'];
unset($invoiceInfo['line']);
unset($invoiceInfo['documentTotals']);

$insertedInvoice = new Insert('Invoice', $invoiceInfo);
$invoiceId = getId('Invoice', 'invoiceNo', $invoiceInfo['invoiceNo']);

foreach($invoiceLines as $line) {
    // INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
    $fields = array(
        'invoiceId' => $invoiceId,
        'productId' => getId('Product', 'productCode', $line['productCode']),
        'quantity'  => $line['quantity'],
        'taxId'     => getId('Tax', 'taxType', $line['tax']['taxType'])
    );
    $insertedLines = new Insert('InvoiceLine', $fields);
}

$invoiceUrl = getAPIUrl('Invoice', 'InvoiceNo', $invoiceInfo['invoiceNo']);
$invoiceUpdated = file_get_contents($invoiceUrl);
echo $invoiceUpdated;