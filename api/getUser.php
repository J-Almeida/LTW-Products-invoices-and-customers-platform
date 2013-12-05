<?php
require_once 'user.php';

$value = NULL;
if ( isset($_GET['Username']) && !empty($_GET['Username']) ) {
    $value = $_GET['Username'];
} else {
    $error = new Error(700, "Expected Username parameter");
    die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
}

echo json_encode(getUser($value), JSON_NUMERIC_CHECK);