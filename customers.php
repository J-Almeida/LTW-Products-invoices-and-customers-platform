<?php

include_once "searches.php";

$fields = array(
    'customerId' => "ID",
    'customerTaxId' => 'Tax ID',
    'companyName' => 'Name',
    'email' => 'Email',
    'addressDetail' => 'Address',
    'cityName' => 'City',
    'countryName' => 'Country');

echo getSearchPage("Customers", $fields);