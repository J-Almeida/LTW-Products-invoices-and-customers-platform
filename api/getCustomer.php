<?php
include 'utilities.php';
include 'search.php';

$value = NULL;
if ( isset($_GET['CustomerID']) && !empty($_GET['CustomerID']) ) {
    $value = $_GET['CustomerID'];
} else {
    $error = new InvalidSearch(700, "Expected CustomerID parameter");
    die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
}

// Fetch the invoice we are looking for
$table = 'Customer';
$field = 'CustomerID';
$values = array($value);
$rows = array('companyName', 'customerTaxId', 'email', 'addressDetail', 'cityName', 'countryName', 'postalCode' );
$joins = array( 'Customer' => 'BillingAddress', 'BillingAddress' => array('City', 'Country') );

$search = new EqualSearch($table, $field, $values, $rows, $joins);
$result = $search->getResults();

if (!$result) {
    $error = new InvalidSearch(404, "Product not found");
    die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
}

$result = $result[0];

echo json_encode($result, JSON_NUMERIC_CHECK);