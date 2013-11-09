<?php
include 'utilities.php';
include 'search.php';

$parameters = getSearchParameters();
$parameters['table'] = 'Customer';
$parameters['rows'] = array('CustomerID', 'customerTaxID', 'CompanyName');
$parameters['joins'] = array();

$result = executeSearch($parameters);

if (!$result)
    echo '[]';
else
    echo json_encode($result);