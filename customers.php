<?php
require_once 'bootstrap.php';
require_once "searches.php";
require_once './api/authenticationUtilities.php';

$neededPermissions = array('read');
evaluateSessionPermissions($neededPermissions);

$fields = array(
    'CustomerID' => "ID",
    'CustomerTaxID' => 'Tax ID',
    'CompanyName' => 'Name',
    'Email' => 'Email',
    'AddressDetail' => 'Address',
    'CityName' => 'City',
    'CountryName' => 'Country');

echo getSearchPage("Customers", $fields);