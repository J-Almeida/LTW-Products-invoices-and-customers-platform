<?php
include 'utilities.php';
include 'search.php';

$parameters = getSearchParametersFromURL();

$parameters['table'] = 'Customer';
$parameters['rows'] = array('customerTaxID', 'companyName', 'addressDetail', 'cityName', 'countryName');
$parameters['joins'] = array('Customer' => 'BillingAddress', 'BillingAddress' => array('City', 'Country'));

$result = executeSearch($parameters);

if (!$result)
    echo '[]';
else
    echo json_encode($result, JSON_NUMERIC_CHECK);