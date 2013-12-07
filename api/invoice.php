<?php
require_once 'search.php';
require_once 'utilities.php';
require_once 'insert.php';
require_once 'update.php';
require_once 'delete.php';

function getInvoice($invoiceNo) {
    // Fetch the invoice we are looking for
    $table = 'Invoice';
    $field = 'invoiceNo';
    $values = array($invoiceNo);
    $rows = array('invoiceId', 'invoiceNo', 'invoiceDate', 'customerId', 'taxPayable', 'netTotal', 'grossTotal');
    $joins = array();

    $invoiceSearch = new EqualSearch($table, $field, $values, $rows, $joins);
    $invoice = $invoiceSearch->getResults();

    if (!$invoice) {
        $error = new Error(404, "Invoice not found");
        return $error->getInfo();
        //die(json_encode($error->getInfo(), JSON_NUMERIC_CHECK));
    }

    $invoice = $invoice[0];

    roundDocumentTotals($invoice);

    // Fetch the invoice lines associated with the invoice found
    $table = 'InvoiceLine';
    $field = 'invoiceId';
    $values = array($invoice['invoiceId']);
    $rows = array('lineNumber', 'productCode', 'quantity', 'unitPrice', 'creditAmount' , 'Tax.taxId AS taxId', 'taxType', 'taxPercentage');
    $joins = array('InvoiceLine' => array('Tax', 'Product'));

    $invoiceLinesSearch = new EqualSearch($table, $field, $values, $rows, $joins);
    $invoiceLines = $invoiceLinesSearch->getResults();
    foreach($invoiceLines as &$invoiceLine){
        roundLineTotals($invoiceLine);
        setValuesAsArray('tax', array('taxType', 'taxPercentage'), $invoiceLine);
    }

    unset($invoice['invoiceId']);
    $invoice['line'] = $invoiceLines;

    setValuesAsArray('documentTotals', array('taxPayable', 'netTotal', 'grossTotal' ), $invoice);

    return $invoice;
}

function insertInvoice($invoiceInfo) {
    $invoiceLines = $invoiceInfo['line'];
    unset($invoiceInfo['line']);
    unset($invoiceInfo['documentTotals']);

    new Insert('Invoice', $invoiceInfo);
    $invoiceId = getId('Invoice', 'invoiceNo', $invoiceInfo['invoiceNo']);

    foreach($invoiceLines as $line) {
        if($line['taxId'])
            $taxId = $line['taxId'];
        else
            $taxId = getId('Tax', 'taxType', $line['tax']['taxType']);

        $fields = array(
            'invoiceId' => $invoiceId,
            'productId' => getId('Product', 'productCode', $line['productCode']),
            'quantity'  => $line['quantity'],
            'taxId'     => $taxId
        );
        new Insert('InvoiceLine', $fields);
    }

    return getInvoice($invoiceInfo['invoiceNo']);
}

function updateInvoice($invoiceInfo) {

// TODO select only the necessary fields from the json, return error when important fields are missing

    $table = 'Invoice';
    $field = 'InvoiceNo';
    $invoiceNo = $invoiceInfo['invoiceNo'];

    if ($invoiceNo == NULL) {
        // create a new invoice with the last invoiceNo + 1
        $invoiceNo = getLastInvoiceNoPlusOne();
        $invoiceInfo['invoiceNo'] = $invoiceNo;
        $response = insertInvoice($invoiceInfo);
        return $response;
    }

    $invoiceId = getId('Invoice', 'invoiceNo', $invoiceNo);
    $invoiceLines = $invoiceInfo['line'];

    // ignore and reset document totals and lines
    unset($invoiceInfo['line']);
    unset($invoiceInfo['documentTotals']);
    $invoiceInfo['taxPayable'] = 0;
    $invoiceInfo['netTotal'] = 0;
    $invoiceInfo['grossTotal'] = 0;

    new Update($table, $invoiceInfo, $field, $invoiceNo);

    // Re insert all invoice lines
    // This is necessary because the database will calculate the new invoice totals
    new Delete('InvoiceLine', array('invoiceId' => $invoiceId));

    foreach($invoiceLines as $line) {
        // INSERT INTO InvoiceLine(invoiceId, productId, quantity, taxId)
        if($line['taxId'])
            $taxId = $line['taxId'];
        else
            $taxId = getId('Tax', 'taxType', $line['tax']['taxType']);

        $fields = array(
            'invoiceId' => $invoiceId,
            'productId' => getId('Product', 'productCode' ,$line['productCode']),
            'quantity'  => $line['quantity'],
            'taxId'     => $taxId
        );
        new Insert('InvoiceLine', $fields);
    }

    return getInvoice($invoiceNo);
}

function getLastInvoiceNo(){
    $table = 'Invoice';
    $field = 'invoiceNo';
    $values = array();
    $rows = array('invoiceNo');
    $invoiceSearch = new MaxSearch($table, $field, $values, $rows);
    $results = $invoiceSearch->getResults();
    if(isSet($results[0])) {
        return $results[0]['invoiceNo'];
    }
    return null;
}

function getLastInvoiceNoPlusOne() {
    // TODO handle case when there isn't any invoice!
    $invoiceNo = getLastInvoiceNo();
    $matches = array();
    preg_match('/(\d+)$/', $invoiceNo, $matches);
    $invoiceNo = substr($invoiceNo, 0, strpos($invoiceNo, $matches[0]) );
    $invoiceNumber = (float)$matches[0] + 1;
    $invoiceNo .= $invoiceNumber;
    return $invoiceNo;
}