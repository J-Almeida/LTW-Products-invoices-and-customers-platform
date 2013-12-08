<?php
require_once 'search.php';
require_once 'utilities.php';
require_once 'update.php';
require_once 'insert.php';

function getCustomer($customerId) {
    // Fetch the customer we are looking for
    $table = 'Customer';
    $field = 'CustomerID';
    $values = array($customerId);
    $rows = array('CustomerID', 'CompanyName', 'CustomerTaxID', 'Email', 'AddressDetail', 'City', 'Country.CountryID AS CountryID', 'CountryName', 'Country', 'PostalCode' );
    $joins = array('Customer' => 'Country');

    $search = new EqualSearch($table, $field, $values, $rows, $joins);
    $result = $search->getResults();

    if (!$result) {
        $error = new Error(404, "Customer not found");
        return $error->getInfo();
        //die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
    }

    $result = $result[0];

    setValuesAsArray('BillingAddress', array('AddressDetail', 'City', 'PostalCode', 'Country', 'CountryName'), $result);

    return $result;
}

function updateCustomer($customerInfo) {
// TODO select only the necessary fields from the json, return error when important fields are missing

    $table = 'Customer';
    $field = 'CustomerID';
    
    if(isset($customerInfo['CustomerID']))
        $customerId = $customerInfo['CustomerID'];
    else
        $customerId = NULL;

    if ($customerId == NULL) {
        $customerInfo['CustomerID'] = getLastCustomerId() + 1;
        new Insert('Customer', $customerInfo);
        $customerId = $customerInfo['CustomerID'];
    } else
        new Update($table, $customerInfo, $field, $customerId);

    return getCustomer($customerId);
}

function getLastCustomerId(){
    $table = 'Customer';
    $field = 'CustomerID';
    $values = array();
    $rows = array('CustomerID');
    $max = new MaxSearch($table, $field, $values, $rows);
    $results = $max->getResults();
    if(isSet($results[0])) {
        return $results[0]['CustomerID'];
    }
    return 0;
}