<?php
include 'utilities.php';
include 'search.php';

$invoiceNo = NULL;
if ( isset($_GET['InvoiceNo']) && !empty($_GET['InvoiceNo']) ) {
    $invoiceNo = $_GET['InvoiceNo'];
}

// Fetch the invoice we are looking for
$table = 'Invoice';
$field = 'invoiceNo';
$values = array($invoiceNo);
$rows = array('invoiceId','invoiceNo', 'invoiceDate', 'customerID', 'taxPayable', 'netTotal', 'grossTotal');
$joins = array();

$invoiceSearch = new EqualSearch($table, $field, $values, $rows, $joins);
$invoice = $invoiceSearch->getResults();

if (!$invoice) {
    $error = new InvalidSearch(404, "Invoice not found");
    die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
}

$invoice = $invoice[0];

// Fetch the invoice lines associated with the invoice found
$table = 'InvoiceLine';
$field = 'invoiceId';
$values = array($invoice['invoiceId']);
$rows = array('lineNumber', 'productCode', 'quantity', 'unitPrice', 'creditAmount' , 'taxType', 'taxPercentage');
$joins = array('InvoiceLine' => array('Tax', 'Product'));

$invoiceLinesSearch = new EqualSearch($table, $field, $values, $rows, $joins);
$invoiceLines = $invoiceLinesSearch->getResults();
foreach($invoiceLines as &$invoiceLine){
    setValuesAsArray('tax', array('taxType', 'taxPercentage'), $invoiceLine);
}

unset($invoice['invoiceId']);
$invoice['Line'] = $invoiceLines;

setValuesAsArray('DocumentTotals', array('taxPayable', 'netTotal', 'grossTotal' ), $invoice);

echo json_encode($invoice, JSON_NUMERIC_CHECK);