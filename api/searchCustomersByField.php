<?php
require_once '../bootstrap.php';
require_once 'utilities.php';
require_once 'search.php';
require_once 'authenticationUtilities.php';

if(!comparePermissions(array('read'))) {
    $error = new Error(601, 'Permission denied');
    die( json_encode($error->getInfo()) );
}

$parameters = getSearchParametersFromURL();

$parameters['table'] = 'Customer';
$parameters['rows'] = array('CustomerID', 'CustomerTaxID', 'CompanyName', 'AddressDetail', 'CityName', 'CountryName', 'Email');
$parameters['joins'] = array('Customer' => 'Country');

$result = executeSearch($parameters);

if (!$result)
    echo '[]';
else
    echo json_encode($result);