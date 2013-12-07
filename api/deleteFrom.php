<?php
require_once '../bootstrap.php';
require_once 'authenticationUtilities.php';
require_once 'delete.php';
require_once 'update.php';

$table = NULL;
if ( isset($_GET['table']) && !empty($_GET['table']) ) {
    $table = $_GET['table'];
} else {
    $error = new Error(700, 'Missing \'table\' field');
    die( json_encode($error->getInfo()) );
}

$field = NULL;
if ( isset($_GET['field']) && !empty($_GET['field']) ) {
    $field = $_GET['field'];
}

$value = NULL;
if ( isset($_GET['value']) && !empty($_GET['value']) ) {
    $value = $_GET['value'];
}

if($table == "User") {
    if(!comparePermissions(array('promote'))) {
        $error = new Error(601, 'Permission denied');
        die( json_encode($error->getInfo()) );
    }
}
else {
    if(!comparePermissions(array('write'))) {
        $error = new Error(601, 'Permission denied');
        die( json_encode($error->getInfo()) );
    }
}

// handle product deletions
if ($table == 'Product' && $field != null && $value != null) {
    $search = new EqualSearch('Product', $field, array($value), array('*'));
    $results = $search->getResults();
    foreach($results as $product) {
        $productId = $product['productId'];
        $search = new EqualSearch('InvoiceLine', 'productId', array($productId), array('*') );
        $invoiceLines = $search->getResults();
        foreach($invoiceLines as $line) {
            // when a product is deleted, the line from the invoice will be too
            // so we must update the invoice totals
            $taxSearch = new EqualSearch('Tax', 'taxId', array($line['taxId']), array('taxPercentage'));
            $results = $taxSearch->getResults();
            if(isSet($results[0])) {
                $taxPercentage = $results[0]['taxPercentage'];
            }

            $productTaxPayable = $line['creditAmount'] * 0.01 * $taxPercentage;
            $productNetTotal = $line['creditAmount'];

            $invoiceSearch = new EqualSearch('Invoice', 'invoiceId', array($line['invoiceId']), array('*'));
            $results = $invoiceSearch->getResults();
            if(isSet($results[0])) {
                $invoice = $results[0];
            }
            $invoice['taxPayable'] = $invoice['taxPayable'] - $productTaxPayable;
            $invoice['netTotal'] = $invoice['netTotal'] - $productNetTotal;
            $invoice['grossTotal'] = $invoice['netTotal'] + $invoice['taxPayable'];

            new Update('Invoice', $invoice, 'invoiceId', $invoice['invoiceId']);
        }
    }
}

if ($field == null || $value == null)
    new Delete($table, array());
else
    new Delete($table, array($field => $value));