<?php
session_start();

require_once 'invoice.php';

if(!isset($_SESSION['username']) || !isset($_SESSION['permissions']) || $_SESSION['permissions']['write'] != '1') {
    $error = new Error(601, 'Permission Denied');
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
echo json_encode(updateInvoice($invoiceInfo), JSON_NUMERIC_CHECK);