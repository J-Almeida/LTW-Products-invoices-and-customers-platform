<?php
include_once 'utilities.php';
include_once 'search.php';

$value = NULL;
if ( isset($_GET['Username']) && !empty($_GET['Username']) ) {
    $value = $_GET['Username'];
} else {
    $error = new Error(700, "Expected Username parameter");
    die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
}

// Fetch the user we are looking for
$table = 'User';
$field = 'username';
$values = array($value);
$rows = array('username', 'name', 'email', 'permissionType');
$joins = array('User' => 'Permission');

$search = new EqualSearch($table, $field, $values, $rows, $joins);
$result = $search->getResults();

if (!$result) {
    $error = new Error(404, "User not found");
    die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
}

$result = $result[0];

echo json_encode($result, JSON_NUMERIC_CHECK);