<?php
require_once 'error.php';

if ($_FILES['file']['error'] == UPLOAD_ERR_OK               //checks for errors
    && is_uploaded_file($_FILES['file']['tmp_name'])) { //checks that file is uploaded
    $contents = file_get_contents($_FILES['file']['tmp_name']);
}

// functions libxml_display_error() and libxml_display_errors() taken from php documentation
// http://us1.php.net/manual/en/domdocument.schemavalidate.php
function libxml_display_error($error)
{
    $return = "<br/>\n";
    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "<b>Warning $error->code</b>: ";
            break;
        case LIBXML_ERR_ERROR:
            $return .= "<b>Error $error->code</b>: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "<b>Fatal Error $error->code</b>: ";
            break;
    }
    $return .= trim($error->message);
    if ($error->file) {
        $return .=    " in <b>$error->file</b>";
    }
    $return .= " on line <b>$error->line</b>\n";

    return $return;
}

function libxml_display_errors() {
    $errors = libxml_get_errors();
    foreach ($errors as $error) {
        print libxml_display_error($error);
    }
    libxml_clear_errors();
}

// Enable user error handling
libxml_use_internal_errors(true);

// Extend the time limit for this script
set_time_limit(300);

$xml= new DOMDocument();
$xml->loadXML($contents, LIBXML_NOBLANKS); // Or load if filename required

if (!$xml->schemaValidate('./saft.xsd')){
    print '<b>Saft file is not valid! Generated Errors report:</b><br/>';
    libxml_display_errors();
} else {
    $auditFile = new SimpleXMLElement($contents);

    // these arrays have indexes with the old Id's (the imported database Id's or unique fields)
    // and store the values of the new Id's (our database unique fields)
    $customers = array();
    $products = array();
    $invoices = array();

    require_once 'search.php';
    require_once 'customer.php';
    require_once 'product.php';
    require_once 'invoice.php';

    foreach($auditFile->MasterFiles->Customer as $customer) {
        $oldCustomerId = (int) $customer->CustomerID;

        // check if the customer already exists in our DB (by tax ID)
        $existingCustomer = getObject('Customer', 'CustomerTaxID', $customer->CustomerTaxID);

        if ($existingCustomer) {
            // if tax ID is found, we already have this customer
            $customers[$oldCustomerId] = json_decode($existingCustomer, true);
            echo "Customer with ID $oldCustomerId already in current database with ID ".$customers[$oldCustomerId]['CustomerID'].'<br/>';
        } else {
            // if not found, we parse the new customer and add him to our DB
            $customerToImport = array(
                'CompanyName' => (string) $customer->CompanyName,
                'CustomerTaxID' => (int) $customer->CustomerTaxID,
                'Email' => (string) $customer->Email,
                'AddressDetail' => (string) $customer->BillingAddress->AddressDetail,
                'City' => (string) $customer->BillingAddress->City,
                'CountryID' => getCountryId($customer->BillingAddress->Country),
                'PostalCode' => (string) $customer->BillingAddress->PostalCode
            );

            $response = updateCustomer($customerToImport);
            if (!isset($response['error']) || empty($response['error'])) {
                $customers[$oldCustomerId] = $response;
                echo "Imported customer with ID $oldCustomerId as ".$response['CustomerID'].'<br/>';
            } else {
                echo "Error inserting customer with ID $oldCustomerId:<br/>";
                echo 'Error code '.$response['error']['code'].': '.$response['error']['reason'].'<br/>';
            }
        }
    }

    foreach($auditFile->MasterFiles->Product as $product) {
        $oldProductCode = (int) $product->ProductCode;

        $existingProduct = getObject('Product', 'ProductDescription', $product->ProductDescription);

        if ($existingProduct) {
            $products[$oldProductCode] = json_decode($existingProduct, true);
            echo "Product with code $oldProductCode already in current database with code ".$products[$oldProductCode]['ProductCode'].'<br/>';
        } else {
            $productInfo = getProductInfo((int)$product->ProductCode, $auditFile);
            if ($productInfo == null) {
                continue; // not a valid product, skip
            }
            $productToImport = array(
                'ProductDescription' => (string) $product->ProductDescription,
                'UnitPrice' => $productInfo['UnitPrice'],
                'UnitOfMeasure' => $productInfo['UnitOfMeasure']
            );

            $response = updateProduct($productToImport);
            if (!isset($response['error']) || empty($response['error'])) {
                $products[$oldProductCode] = $response;
                echo "Imported product with code $oldProductCode as ".$response['ProductCode'].'<br/>';
            } else {
                echo "Error inserting product with code $oldProductCode:<br/>";
                echo 'Error code '.$response['error']['code'].': '.$response['error']['reason'].'<br/>';
            }
        }
    }

    foreach($auditFile->MasterFiles->TaxTable->TaxTableEntry as $tax) {
        $taxType = (string) $tax->TaxType;
        $search = new EqualSearch('Tax', 'TaxType', array($taxType), array('TaxID'));
        $results = $search->getResults();
        if(!isset($results[0]) || !$results[0] ) { // tax doesn't exist in the DB
            $newTax = array(
                'TaxType' =>  (string) $tax->TaxType,
                'TaxPercentage' =>  (float) $tax->TaxPercentage,
                'TaxDescription' => (string) $tax->Description
            );
            new Insert('Tax', $newTax);
        }
    }

    foreach($auditFile->SourceDocuments->SalesInvoices->Invoice as $invoice) {
        $lines = array();
        foreach($invoice->Line as $line) {
            $importedLine = array(
                'LineNumber' => (int) $line->LineNumber,
                'ProductCode' => $products[(int)$line->ProductCode]['ProductCode'],
                'Quantity' => (int) $line->Quantity,
                'UnitPrice' => (float) $line->UnitPrice,
                'CreditAmount' => (float) $line->CreditAmount,
                'Tax' => array(
                    'TaxType' => (string) $line->Tax->TaxType,
                    'TaxPercentage' => (float) $line->Tax->TaxPercentage
                )
            );
            array_push($lines, $importedLine);
        }

        $invoiceToImport = array(
            'InvoiceDate' => (string) $invoice->InvoiceDate,
            'CustomerID' => $customers[(int)$invoice->CustomerID]['CustomerID'],
            'Line' => $lines,
            'DocumentTotals' => array(
                'TaxPayable' => (float) $invoice->DocumentTotals->TaxPayable,
                'NetTotal' => (float) $invoice->DocumentTotals->NetTotal,
                'GrossTotal' => (float) $invoice->DocumentTotals->GrossTotal
            )
        );

        $response = updateInvoice($invoiceToImport);
        if (isset($response['error']) && !empty($response['error'])) {
            echo "Error inserting product with number $invoice->InvoiceNo:<br/>";
            echo 'Error code '.$response['error']['code'].': '.$response['error']['reason'].'<br/>';
        } else {
            echo "Imported invoice with number $invoice->InvoiceNo as ".$response['InvoiceNo'].'<br/>';
        }
    }
}

function getProductInfo($productCode, $auditFile) {
    foreach($auditFile->SourceDocuments->SalesInvoices->Invoice as $invoice) {
        foreach($invoice->Line as $line) {
            if ($productCode == (int)$line->ProductCode) {
                $productInfo = array();
                $productInfo['UnitPrice'] = (float) $line->UnitPrice;
                $productInfo['UnitOfMeasure'] = (string) $line->UnitOfMeasure;
                return $productInfo;
            }
        }
    }
    return null;
}

function getObject($table, $field, $value) {
    $search = new EqualSearch($table, $field, array($value), array('*'));
    $results = $search->getResults();
    if(!isset($results[0]) || !$results[0]) {
        return null;
    }
    $results = json_encode($results[0]);
    return $results;
}

?>