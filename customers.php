<?php

include "searches.php";

$fields = array('customerTaxId' => 'Customer Tax ID',
    'companyName' => 'Company Name',
    'email' => 'Email',
    'permissionType' => 'Permissions',
    'addressDetail' => 'Address',
    'cityName' => 'City',
    'countryName' => 'Country');

echo getSearchPage("Customers", $fields);