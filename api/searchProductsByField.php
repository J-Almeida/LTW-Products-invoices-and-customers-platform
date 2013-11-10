<?php
include 'utilities.php';
include 'search.php';

$parameters = getSearchParametersFromURL();

$parameters['table'] = 'Product';
$parameters['rows'] = array('ProductCode', 'ProductDescription', 'UnitPrice');
$parameters['joins'] = array();

$result = executeSearch($parameters);

if (!$result)
    echo '[]';
else
    echo json_encode($result);