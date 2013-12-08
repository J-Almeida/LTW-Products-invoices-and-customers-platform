<?php
require_once 'search.php';
require_once 'utilities.php';
require_once 'update.php';
require_once 'insert.php';
require_once 'delete.php';

function getProduct($productCode) {
    // Fetch the product we are looking for
    $table = 'Product';
    $field = 'ProductCode';
    $values = array($productCode);
    $rows = array('ProductCode','ProductDescription', 'UnitPrice', 'UnitOfMeasure');
    $joins = array();

    $search = new EqualSearch($table, $field, $values, $rows, $joins);
    $result = $search->getResults();

    if (!$result) {
        $error = new Error(404, "Product not found");
        return $error->getInfo();
        //die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
    }

    $result = $result[0];

    roundProductTotals($result);

    return $result;
}

function updateProduct($productInfo) {

    $table = 'Product';
    $field = 'ProductCode';
    if(isset($productInfo['ProductCode']))
        $productCode = $productInfo['ProductCode'];
    else
        $productCode = NULL;

    $obligatoryFields = array('ProductDescription');
    $optionalFields = array('ProductCode', 'UnitPrice', 'UnitOfMeasure');
    validateFields($productInfo, $obligatoryFields, $optionalFields);

    if ($productCode == NULL) {
        $productCode = getLastProductCode() + 1;
        $productInfo['ProductCode'] = $productCode;
        new Insert('Product', $productInfo);
    } else {
        $search = new EqualSearch('Product', 'ProductCode', array($productInfo['ProductCode']), array('*'));
        $results = $search->getResults();
        $oldProduct = $results[0];
        new Update($table, $productInfo, $field, $productCode);
        updateInvoicesOnPriceChange($productInfo, $oldProduct);
    }

    return getProduct($productCode);
}

function updateInvoicesOnPriceChange($productInfo, $oldProduct) {
    if ($oldProduct['UnitPrice'] == $productInfo['UnitPrice']) {
        return;
    }
    $productId = $oldProduct['ProductID'];
    $search = new EqualSearch('InvoiceLine', 'ProductID', array($productId), array('*') );
    $invoiceLines = $search->getResults();
    foreach($invoiceLines as $line) {
        // update the invoice totals
        $invoiceInfo = array();
        $invoiceInfo['TaxPayable'] = 0;
        $invoiceInfo['NetTotal'] = 0;
        $invoiceInfo['GrossTotal'] = 0;
        new Update('Invoice', $invoiceInfo, 'InvoiceID', $line['InvoiceID']);

        $search = new EqualSearch('InvoiceLine', 'InvoiceID', array($line['InvoiceID']), array('*'));
        $newLines = $search->getResults();

        new Delete('InvoiceLine', array('InvoiceID' => $line['InvoiceID']));
        foreach ($newLines as $line) {
            unset($line['CreditAmount']);
            new Insert('InvoiceLine', $line);
        }
    }

}

function getLastProductCode(){
    $table = 'Product';
    $field = 'ProductCode';
    $values = array();
    $rows = array('ProductCode');
    $max = new MaxSearch($table, $field, $values, $rows);
    $results = $max->getResults();
    if(isSet($results[0])) {
        return $results[0]['ProductCode'];
    }
    return 0;
}