<?php
require_once 'bootstrap.php';
require_once "searches.php";
require_once './api/authenticationUtilities.php';

$neededPermissions = array('read');
evaluateSessionPermissions($neededPermissions);

$fields = array(
    'customerId' => "ID",
    'customerTaxId' => 'Tax ID',
    'companyName' => 'Name',
    'email' => 'Email',
    'addressDetail' => 'Address',
    'cityName' => 'City',
    'countryName' => 'Country');

echo getSearchPage("Customers", $fields);