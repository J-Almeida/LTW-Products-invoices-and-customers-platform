<?php

include "searches.php";

$fields = array(
    'customerId' => "ID",
    'customerTaxId' => 'Tax ID',
    'companyName' => 'Name',
    'email' => 'Email',
    'permissionType' => 'Permissions',
    'addressDetail' => 'Address',
    'cityName' => 'City',
    'countryName' => 'Country');

echo getSearchPage("Customers", $fields);