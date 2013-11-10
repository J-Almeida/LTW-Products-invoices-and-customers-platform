<?php
include 'utilities.php';
include 'search.php';

$parameters = getSearchParametersFromURL();

$parameters['table'] = 'Customer';
$parameters['rows'] = array('CustomerID', 'customerTaxID', 'CompanyName', 'addressDetail', 'cityName');
$parameters['joins'] = array('Customer' => 'BillingAddress', 'BillingAddress' => 'City');

$result = executeSearch($parameters);

if (!$result)
    echo '[]';
else
    echo json_encode($result, JSON_NUMERIC_CHECK);