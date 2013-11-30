<?php
include_once 'utilities.php';
include_once 'search.php';

$parameters = getSearchParametersFromURL();

$parameters['table'] = 'Customer';
$parameters['rows'] = array('customerId', 'customerTaxID', 'companyName', 'addressDetail', 'cityName', 'countryName', 'email');
$parameters['joins'] = array();

$result = executeSearch($parameters);

if (!$result)
    echo '[]';
else
    echo json_encode($result, JSON_NUMERIC_CHECK);