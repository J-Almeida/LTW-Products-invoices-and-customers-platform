<?php
include 'utilities.php';
include 'search.php';

$parameters = getSearchParametersFromURL();

$parameters['table'] = 'Invoice';
$parameters['rows'] = array('InvoiceNo', 'InvoiceDate', 'taxPayable', 'netTotal' ,'GrossTotal', 'CompanyName');
$parameters['joins'] = array('Invoice' => 'Customer');

$result = executeSearch($parameters);

// round the invoice totals
foreach ($result as &$invoice) {
    roundDocumentTotals($invoice);
}

if (!$result)
    echo '[]';
else
    echo json_encode($result, JSON_NUMERIC_CHECK);