<?php
require_once 'search.php';
require_once 'utilities.php';
require_once 'insert.php';
require_once 'update.php';
require_once 'delete.php';

function getInvoice($invoiceNo) {
    // Fetch the invoice we are looking for
    $table = 'Invoice';
    $field = 'InvoiceNo';
    $values = array($invoiceNo);
    $rows = array('InvoiceID', 'InvoiceNo', 'InvoiceDate', 'CustomerID', 'TaxPayable', 'NetTotal', 'GrossTotal');
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
    $field = 'InvoiceID';
    $values = array($invoice['InvoiceID']);
    $rows = array('LineNumber', 'ProductCode', 'Quantity', 'UnitPrice', 'CreditAmount' , 'Tax.TaxID AS TaxID', 'TaxType', 'TaxPercentage');
    $joins = array('InvoiceLine' => array('Tax', 'Product'));

    $invoiceLinesSearch = new EqualSearch($table, $field, $values, $rows, $joins);
    $invoiceLines = $invoiceLinesSearch->getResults();
    foreach($invoiceLines as &$invoiceLine){
        roundLineTotals($invoiceLine);
        setValuesAsArray('Tax', array('TaxType', 'TaxPercentage'), $invoiceLine);
    }

    unset($invoice['InvoiceID']);
    $invoice['Line'] = $invoiceLines;

    setValuesAsArray('DocumentTotals', array('TaxPayable', 'NetTotal', 'GrossTotal' ), $invoice);

    return $invoice;
}

function insertInvoice($invoiceInfo) {
    $invoiceLines = $invoiceInfo['Line'];
    unset($invoiceInfo['Line']);
    unset($invoiceInfo['DocumentTotals']);

    new Insert('Invoice', $invoiceInfo);
    $invoiceId = getId('Invoice', 'InvoiceNo', $invoiceInfo['InvoiceNo']);

    foreach($invoiceLines as $line) {
        $taxId = getTaxId($line);

        $fields = array(
            'InvoiceID' => $invoiceId,
            'ProductID' => getId('Product', 'ProductCode', $line['ProductCode']),
            'Quantity'  => $line['Quantity'],
            'TaxID'     => $taxId
        );
        new Insert('InvoiceLine', $fields);
    }

    return getInvoice($invoiceInfo['InvoiceNo']);
}

function updateInvoice($invoiceInfo) {
// TODO select only the necessary fields from the json, return error when important fields are missing

    $table = 'Invoice';
    $field = 'InvoiceNo';
    if(isset($invoiceInfo['InvoiceNo']) && !empty($invoiceInfo['InvoiceNo']))
        $invoiceNo = $invoiceInfo['InvoiceNo'];
    else
        $invoiceNo = NULL;

    if ($invoiceNo == NULL) {
        // create a new invoice with the last invoiceNo + 1
        $invoiceNo = getLastInvoiceNoPlusOne();
        $invoiceInfo['InvoiceNo'] = $invoiceNo;
        $response = insertInvoice($invoiceInfo);
        return $response;
    }

    $invoiceId = getId('Invoice', 'InvoiceNo', $invoiceNo);
    $invoiceLines = $invoiceInfo['Line'];

    // ignore and reset document totals and lines
    unset($invoiceInfo['Line']);
    unset($invoiceInfo['DocumentTotals']);
    $invoiceInfo['TaxPayable'] = 0;
    $invoiceInfo['NetTotal'] = 0;
    $invoiceInfo['GrossTotal'] = 0;

    new Update($table, $invoiceInfo, $field, $invoiceNo);

    // Re insert all invoice lines
    // This is necessary because the database will calculate the new invoice totals
    new Delete('InvoiceLine', array('InvoiceID' => $invoiceId));

    foreach($invoiceLines as $line) {
        $taxId = getTaxId($line);

        $fields = array(
            'InvoiceID' => $invoiceId,
            'ProductID' => getId('Product', 'ProductCode' ,$line['ProductCode']),
            'Quantity'  => $line['Quantity'],
            'TaxID'     => $taxId
        );
        new Insert('InvoiceLine', $fields);
    }

    return getInvoice($invoiceNo);
}

function getLastInvoiceNo(){
    $table = 'Invoice';
    $field = 'InvoiceNo';
    $values = array();
    $rows = array('InvoiceNo');
    $invoiceSearch = new ListAllSearch($table, $field, $values, $rows);
    $results = $invoiceSearch->getResults();
    $maxInv = 'FT SEQ/0';
    $maxInvNo = 0;
    if(isset($results) && !empty($results)) {
        foreach($results as $result) {
            $matches = array();
            $invoiceNo = $result['InvoiceNo'];
            preg_match('/(\d+)$/', $invoiceNo, $matches);
            $invoiceNo = substr($invoiceNo, 0, strpos($invoiceNo, $matches[0]) );
            $invoiceNumber = (float)$matches[0] + 1;
            if($invoiceNumber > $maxInvNo) {
                $maxInv = $result['InvoiceNo'];
                $maxInvNo = $invoiceNumber;
            }
        }
    }

    return $maxInv;
}

function getLastInvoiceNoPlusOne() {
    $invoiceNo = getLastInvoiceNo();
    $matches = array();
    preg_match('/(\d+)$/', $invoiceNo, $matches);
    $invoiceNo = substr($invoiceNo, 0, strpos($invoiceNo, $matches[0]) );
    $invoiceNumber = (float)$matches[0] + 1;
    $invoiceNo .= $invoiceNumber;
    return $invoiceNo;
}

function getTaxId($invoiceLine) {
    $taxId = getId('Tax', 'TaxType', $invoiceLine['Tax']['TaxType']);
    if ($taxId == null) {
        $newTax = array(
            'TaxType' =>  $invoiceLine['Tax']['TaxType'],
            'TaxPercentage' => $invoiceLine['Tax']['TaxPercentage'],
            'TaxDescription' => $invoiceLine['Tax']['TaxType']
        );
        new Insert('Tax', $newTax);
        return getId('Tax', 'TaxType', $invoiceLine['Tax']['TaxType']);
    }
    return $taxId;
}