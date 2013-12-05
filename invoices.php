<?php
session_start();
include_once "searches.php";
include_once './api/authenticationUtilities.php';

$neededPermissions = array('read');
evaluateSessionPermissions($neededPermissions);

$fields = array(
    'invoiceNo' => 'Invoice Number',
    'invoiceDate' => 'Invoice Date',
    'companyName' => 'Company Name',
    'taxPayable' => 'Payable tax',
    'netTotal' => 'Net Total',
    'grossTotal' => 'Gross total');

echo getSearchPage("Invoices", $fields);
?>