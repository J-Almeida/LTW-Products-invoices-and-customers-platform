<?php
session_start();

include_once 'update.php';
include_once 'utilities.php';
include_once 'delete.php';
include_once 'search.php';
include_once 'insert.php';
include_once 'authenticationUtilities.php';

if(!comparePermissions(array('write'))) {
    $error = new Error(601, 'Permission Denied');
    die( json_encode($error->getInfo()) );
}

$jsonInvoice = NULL;
if ( isset($_POST['invoice']) && !empty($_POST['invoice']) ) {
    $jsonInvoice = $_POST['invoice'];
} else {
    $error = new Error(700, 'Missing \'invoice\' field');
    die( json_encode($error->getInfo()) );
}

$invoiceInfo = json_decode($jsonInvoice, true);

// TODO select only the necessary fields from the json, return error when important fields are missing

$table = 'Invoice';
$field = 'InvoiceNo';
$invoiceNo = $invoiceInfo['invoiceNo'];

if ($invoiceNo == NULL) {
    // create a new invoice with the last invoiceNo + 1
    $invoiceNo = getLastInvoiceNoPlusOne();
    $invoiceInfo['invoiceNo'] = $invoiceNo;
    $insertUrl = getCurrentPageUrl();
    $insertUrl = substr($insertUrl, 0, strpos($insertUrl, 'api/'));
    $insertUrl .= '/api/insertInvoice.php';
    $response = http_post($insertUrl, array('invoice' => json_encode($invoiceInfo)));
    die($response);
}

$invoiceId = getId('Invoice', 'invoiceNo', $invoiceNo);
$invoiceLines = $invoiceInfo['line'];

// ignore and reset document totals and lines
unset($invoiceInfo['line']);
unset($invoiceInfo['documentTotals']);
$invoiceInfo['taxPayable'] = 0;
$invoiceInfo['netTotal'] = 0;
$invoiceInfo['grossTotal'] = 0;

$update = new Update($table, $invoiceInfo, $field, $invoiceNo);

// Re insert all invoice lines
// This is necessary because the database will calculate the new invoice totals
$deleteLines = new Delete('InvoiceLine', array('invoiceId' => $invoiceId));

foreach($invoiceLines as $line) {
    // INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
    if($line['taxId'])
        $taxId = $line['taxId'];
    else
        $taxId = getId('Tax', 'taxType', $line['tax']['taxType']);

    $fields = array(
        'invoiceId' => $invoiceId,
        'productId' => getId('Product', 'productCode' ,$line['productCode']),
        'quantity'  => $line['quantity'],
        'taxId'     => $taxId
    );
    $insertedLines = new Insert('InvoiceLine', $fields);
}

// call getInvoice to return the updated contents
$invoiceUrl = getAPIUrl('Invoice', 'InvoiceNo', $invoiceNo);
$invoiceUpdated = file_get_contents($invoiceUrl);
echo $invoiceUpdated;

function getLastInvoiceNo(){
    $table = 'Invoice';
    $field = 'invoiceNo';
    $values = array();
    $rows = array('invoiceNo');
    $invoiceSearch = new MaxSearch($table, $field, $values, $rows);
    return $invoiceSearch->getResults()[0]['invoiceNo'];
}

function getLastInvoiceNoPlusOne() {
    $invoiceNo = getLastInvoiceNo();
    $matches = array();
    preg_match('/(\d+)$/', $invoiceNo, $matches);
    $invoiceNo = substr($invoiceNo, 0, strpos($invoiceNo, $matches[0]) );
    $invoiceNumber = (float)$matches[0] + 1;
    $invoiceNo .= $invoiceNumber;
    return $invoiceNo;
}