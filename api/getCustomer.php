<?php
include 'utilities.php';
include 'search.php';

$value = NULL;
if ( isset($_GET['CustomerID']) && !empty($_GET['CustomerID']) ) {
    $value = $_GET['CustomerID'];
} else {
    $error = new Error(700, "Expected CustomerID parameter");
    die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
}

// Fetch the customer we are looking for
$table = 'Customer';
$field = 'CustomerID';
$values = array($value);
$rows = array('customerID', 'companyName', 'customerTaxId', 'email', 'addressDetail', 'cityName', 'countryName', 'postalCode' );
$joins = array();

$search = new EqualSearch($table, $field, $values, $rows, $joins);
$result = $search->getResults();

if (!$result) {
    $error = new Error(404, "Customer not found");
    die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
}

$result = $result[0];

echo json_encode($result, JSON_NUMERIC_CHECK);