<?php
require_once 'bootstrap.php';
require_once "searches.php";
require_once './api/authenticationUtilities.php';

$neededPermissions = array('read');
evaluateSessionPermissions($neededPermissions);

$fields = array(
    'InvoiceNo' => 'Invoice Number',
    'InvoiceDate' => 'Invoice Date',
    'CompanyName' => 'Company Name',
    'TaxPayable' => 'Payable tax',
    'NetTotal' => 'Net Total',
    'GrossTotal' => 'Gross total');

echo getSearchPage("Invoices", $fields);
?>