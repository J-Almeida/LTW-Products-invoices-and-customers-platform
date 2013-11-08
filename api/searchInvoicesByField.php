<?php
include 'utilities.php';

$db = new PDO('sqlite:../database.db');

if ( isset($_GET['field']) && !empty($_GET['field']) ) {
    $field = $_GET['field'];
    var_export($field);
}

if ( isset($_GET['value']) && !empty($_GET['value']) ) {
    $params = retrieveGETparameters('value');
    var_export($params);
}

$result = $db->query("SELECT * FROM Invoice");

if (!$result)
    echo "empty";
else {

    foreach($result as $row) {
        echo json_encode($row);
    }
}

?>