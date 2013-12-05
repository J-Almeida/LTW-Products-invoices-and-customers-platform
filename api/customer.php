<?php
require_once 'search.php';
require_once 'utilities.php';
require_once 'update.php';

function getCustomer($customerId) {
    // Fetch the customer we are looking for
    $table = 'Customer';
    $field = 'CustomerID';
    $values = array($customerId);
    $rows = array('customerID', 'companyName', 'customerTaxId', 'email', 'addressDetail', 'cityName', 'Country.countryId AS countryId', 'countryName', 'countryCode', 'postalCode' );
    $joins = array('Customer' => 'Country');

    $search = new EqualSearch($table, $field, $values, $rows, $joins);
    $result = $search->getResults();

    if (!$result) {
        $error = new Error(404, "Customer not found");
        return $error;
        //die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
    }

    $result = $result[0];

    return $result;
}

function updateCustomer($customerInfo) {
// TODO select only the necessary fields from the json, return error when important fields are missing

    $table = 'Customer';
    $field = 'customerId';
    $customerId = $customerInfo['customerId'];
    if ($customerId == NULL) {
        new Insert('Customer', $customerInfo);
        $customerId = getId('Customer', 'customerTaxId', $customerInfo['customerTaxId']);
    } else
        new Update($table, $customerInfo, $field, $customerId);

    return getCustomer($customerId);
}