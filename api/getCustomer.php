<?php

require_once 'customer.php';

$value = NULL;
if ( isset($_GET['CustomerID']) && !empty($_GET['CustomerID']) ) {
    $value = $_GET['CustomerID'];
} else {
    $error = new Error(700, "Expected CustomerID parameter");
    die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
}

echo json_encode(getCustomer($value), JSON_NUMERIC_CHECK);