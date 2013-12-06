<?php

require_once 'product.php';

require_once '../bootstrap.php';
require_once 'authenticationUtilities.php';

if(!comparePermissions(array('read'))) {
	$error = new Error(601, 'Permission Denied');
    die( json_encode($error->getInfo()) );
}

$value = NULL;
if ( isset($_GET['ProductCode']) && !empty($_GET['ProductCode']) ) {
    $value = $_GET['ProductCode'];
} else {
    $error = new Error(700, "Expected ProductCode parameter");
    die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
}

echo json_encode(getProduct($value), JSON_NUMERIC_CHECK);
