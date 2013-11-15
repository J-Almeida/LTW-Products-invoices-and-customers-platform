<?php
include 'utilities.php';
include 'search.php';

$value = NULL;
if ( isset($_GET['InvoiceNo']) && !empty($_GET['InvoiceNo']) ) {
    $value = $_GET['InvoiceNo'];
} else {
    $error = new InvalidSearch(700, "Expected InvoiceNo parameter");
    die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
}

// Fetch the invoice we are looking for
$table = 'Invoice';
$field = 'invoiceNo';
$values = array($value);
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