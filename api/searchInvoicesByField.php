<?php
include 'utilities.php';
include 'search.php';

$parameters = getSearchParametersFromURL();

$parameters['table'] = 'Invoice';
$parameters['rows'] = array('InvoiceNo', 'InvoiceDate', 'GrossTotal', 'CompanyName');
$parameters['joins'] = array('Invoice' => 'Customer');

$result = executeSearch($parameters);

if (!$result)
    echo '[]';
else
    echo json_encode($result, JSON_NUMERIC_CHECK);