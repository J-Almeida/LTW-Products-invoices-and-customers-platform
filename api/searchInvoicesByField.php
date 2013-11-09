<?php
include 'utilities.php';
include 'search.php';

if ( isset($_GET['field']) && !empty($_GET['field']) ) {
    $field = $_GET['field'];
}

if ( isset($_GET['value']) && !empty($_GET['value']) ) {
    $values = retrieveGETparameters('value');
}

$rows = array('InvoiceNo', 'InvoiceDate', 'GrossTotal', 'CompanyName');
$joins = array('customer');

$search = new RangeSearch('invoice', $field, $values, $rows, $joins);
$result = $search->getResults();

if (!$result)
    echo "empty";
else {
    echo json_encode($result);
}