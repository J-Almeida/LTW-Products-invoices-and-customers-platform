<?php
include 'utilities.php';
include 'search.php';

$value = NULL;
if ( isset($_GET['SupplierID']) && !empty($_GET['SupplierID']) ) {
    $value = $_GET['SupplierID'];
} else {
    $error = new InvalidSearch(700, "Expected SupplierID parameter");
    die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
}

// Fetch the invoice we are looking for
$table = 'Supplier';
$field = 'SupplierID';
$values = array($value);
$rows = array('supplierID', 'companyName', 'supplierTaxId', 'email', 'addressDetail', 'cityName', 'countryName', 'postalCode' );
$joins = array();

$search = new EqualSearch($table, $field, $values, $rows, $joins);
$result = $search->getResults();

if (!$result) {
    $error = new InvalidSearch(404, "Supplier not found");
    die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
}

$result = $result[0];

echo json_encode($result, JSON_NUMERIC_CHECK);