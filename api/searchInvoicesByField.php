<?php
include 'utilities.php';
include 'search.php';

$parameters = getSearchParameters();
$parameters['table'] = 'Invoice';
$parameters['rows'] = array('InvoiceNo', 'InvoiceDate', 'GrossTotal', 'CompanyName');
$parameters['joins'] = array('customer');

$result = executeSearch($parameters);

if (!$result)
    echo '[]';
else
    echo json_encode($result);