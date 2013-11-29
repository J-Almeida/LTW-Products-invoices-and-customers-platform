<?php

include_once 'update.php';
include_once 'utilities.php';
include_once 'delete.php';
include_once 'search.php';
include_once 'insert.php';

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
$invoiceId = getInvoiceId($value);
$invoiceLines = $invoiceInfo['Line'];

// ignore and reset document totals and lines
unset($invoiceInfo['Line']);
unset($invoiceInfo['DocumentTotals']);
$invoiceInfo['taxPayable'] = 0;
$invoiceInfo['netTotal'] = 0;
$invoiceInfo['grossTotal'] = 0;

$update = new Update($table, $invoiceInfo, $field, $value);

// Re insert all invoice lines
// This is necessary because the database will calculate the new invoice totals
$deleteLines = new Delete('InvoiceLine', array('invoiceId' => $invoiceId));

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

// call getInvoice to return the updated contents
$invoiceUrl = getCurrentPageUrl();
$invoiceUrl = substr($invoiceUrl, 0, strpos($invoiceUrl, 'updateInvoice'));
$invoiceUrl .= 'getInvoice.php?InvoiceNo=';
$invoiceUrl .= urlencode($value);

$invoiceUpdated = file_get_contents($invoiceUrl);
echo $invoiceUpdated;


function getInvoiceId($invoiceNo) {
    $table = 'Invoice';
    $field = 'invoiceNo';
    $values = array($invoiceNo);
    $rows = array('invoiceId');
    $invoiceSearch = new EqualSearch($table, $field, $values, $rows);
    return $invoiceSearch->getResults()[0]['invoiceId'];
}

function getProductId($productCode) {
    $table = 'Product';
    $field = 'productCode';
    $values = array($productCode);
    $rows = array('productId');
    $invoiceSearch = new EqualSearch($table, $field, $values, $rows);
    return $invoiceSearch->getResults()[0]['productId'];
}

function getTaxId($taxType) {
    $table = 'Tax';
    $field = 'taxType';
    $values = array($taxType);
    $rows = array('taxId');
    $invoiceSearch = new EqualSearch($table, $field, $values, $rows);
    return $invoiceSearch->getResults()[0]['taxId'];
}