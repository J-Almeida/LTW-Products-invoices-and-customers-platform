<?php

include_once 'insert.php';
include_once 'error.php';
include_once 'utilities.php';

$jsonInvoice = NULL;
// TODO switch to $_POST
if ( isset($_POST['invoice']) && !empty($_POST['invoice']) ) {
    $jsonInvoice = $_POST['invoice'];
} else {
    $error = new Error(700, 'Missing \'invoice\' field');
    die( json_encode($error->getInfo()) );
}

$invoiceInfo = json_decode($jsonInvoice, true);

$table = 'Invoice';
// INSERT INTO Invoice (invoiceNo, invoiceDate, customerId, supplierId)
//              VALUES ("FT SEQ/1", "2013-09-27", 1, 1);
$invoiceLines = $invoiceInfo['Line'];
unset($invoiceInfo['Line']);
unset($invoiceInfo['DocumentTotals']);

$insertedInvoice = new Insert('Invoice', $invoiceInfo);
$invoiceId = getInvoiceId($invoiceInfo['invoiceNo']);

foreach($invoiceLines as $line) {
    // INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
    $fields = array(
        'invoiceId' => $invoiceId,
        'productId' => getProductId($line['productCode']),
        'quantity'  => $line['quantity'],
        'taxId'     => getTaxId($line['tax']['taxType'])
    );
    $insertedLines = new Insert('InvoiceLine', $fields);
}

$invoiceUrl = getInvoiceUrl($invoiceInfo['invoiceNo']);
$invoiceUpdated = file_get_contents($invoiceUrl);
echo $invoiceUpdated;