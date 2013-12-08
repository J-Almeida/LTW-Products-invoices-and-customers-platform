<?php
require_once '../bootstrap.php';

require_once 'invoice.php';
require_once 'authenticationUtilities.php';

if(!comparePermissions(array('write'))) {
    $error = new Error(601, 'Permission denied');
    die( json_encode($error->getInfo()) );
}

$jsonInvoice = NULL;
if ( isset($_POST['invoice']) && !empty($_POST['invoice']) ) {
    $jsonInvoice = $_POST['invoice'];
} else {
    $error = new Error(700, 'Missing \'invoice\' field');
    die( json_encode($error->getInfo()) );
}

$invoiceInfo = json_decode($jsonInvoice, true);
echo json_encode(updateInvoice($invoiceInfo));
