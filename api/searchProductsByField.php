<?php
include 'utilities.php';
include 'search.php';

$parameters = getSearchParametersFromURL();

$parameters['table'] = 'Product';
$parameters['rows'] = array('productCode', 'productDescription', 'unitPrice', 'unitOfMeasure');
$parameters['joins'] = array();

$result = executeSearch($parameters);

foreach($result as &$product) {
    roundProductTotals($product);
}

if (!$result)
    echo '[]';
else
    echo json_encode($result, JSON_NUMERIC_CHECK);