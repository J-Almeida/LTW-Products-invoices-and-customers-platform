<?php
include 'utilities.php';
include 'search.php';

if ( isset($_GET['field']) && !empty($_GET['field']) ) {
    $field = $_GET['field'];
    var_export($field);
}

if ( isset($_GET['value']) && !empty($_GET['value']) ) {
    $values = retrieveGETparameters('value');
    var_export($values);
}

$search = new RangeSearch('invoice', $field, $values);
$result = $search->getResults();

if (!$result)
    echo "empty";
else {

    foreach($result as $row) {
        echo json_encode($row);
    }
}