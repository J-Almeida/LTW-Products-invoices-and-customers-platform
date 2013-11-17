<?php

include "searches.php";

$fields = array(
    'invoiceNo' => 'Invoice Number',
    'invoiceDate' => 'Invoice Date',
    'companyName' => 'Company Name',
    'taxPayable' => 'Payable tax',
    'netTotal' => 'Net Total',
    'grossTotal' => 'Gross total');

echo getSearchPage("Invoices", $fields);