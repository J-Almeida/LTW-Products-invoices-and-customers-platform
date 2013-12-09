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
        $productId = $product['ProductID'];
        $search = new EqualSearch('InvoiceLine', 'ProductID', array($productId), array('*') );
        $invoiceLines = $search->getResults();
        foreach($invoiceLines as $line) {
            // when a product is deleted, the line from the invoice will be too
            // so we must update the invoice totals
            $taxSearch = new EqualSearch('Tax', 'TaxID', array($line['TaxID']), array('TaxPercentage'));
            $results = $taxSearch->getResults();
            if(isSet($results[0])) {
                $taxPercentage = $results[0]['TaxPercentage'];
            }

            $productTaxPayable = $line['CreditAmount'] * 0.01 * $taxPercentage;
            $productNetTotal = $line['CreditAmount'];

            $invoiceSearch = new EqualSearch('Invoice', 'InvoiceID', array($line['InvoiceID']), array('*'));
            $results = $invoiceSearch->getResults();
            if(isSet($results[0])) {
                $invoice = $results[0];
            }
            $invoice['TaxPayable'] = $invoice['TaxPayable'] - $productTaxPayable;
            $invoice['NetTotal'] = $invoice['NetTotal'] - $productNetTotal;
            $invoice['GrossTotal'] = $invoice['NetTotal'] + $invoice['TaxPayable'];

            new Update('Invoice', $invoice, 'InvoiceID', $invoice['InvoiceID']);

            // check for invoices where the only line has the product that will be deleted
            $linesSearch = new EqualSearch('InvoiceLine', 'InvoiceID', array($line['InvoiceID']), array('*'));
            $invoiceUpdatedLines = $linesSearch->getResults();

            if ( count($invoiceUpdatedLines) == 1 && $invoiceUpdatedLines[0]['ProductID'] == $productId ) {
                new Delete('Invoice', array('InvoiceID' => $line['InvoiceID']));
            }
        }
    }
}

if ($field == null || $value == null)
    new Delete($table, array());
else
    new Delete($table, array($field => $value));

header('Refresh: 1; URL=../index.php');

echo "<link rel=\"stylesheet\" href=\"style.css\">";
echo "<br><br><br>";
echo "<p style='text-align: center;'>Deletion completed, redirecting... </p>";
?>