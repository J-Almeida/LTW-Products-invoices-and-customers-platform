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
        $existingCustomer = getObject('Customer', 'customerTaxId', $customer->CustomerTaxID);

        if ($existingCustomer) {
            // if tax ID is found, we already have this customer
            $customers[$oldCustomerId] = json_decode($existingCustomer, true);
            echo "Customer with ID $oldCustomerId already in current database with ID ".$customers[$oldCustomerId]['customerId'].'<br/>';
        } else {
            // if not found, we parse the new customer and add him to our DB
            $customerToImport = array(
                'companyName' => (string) $customer->CompanyName,
                'customerTaxId' => (int) $customer->CustomerTaxID,
                'email' => (string) $customer->Email,
                'addressDetail' => (string) $customer->BillingAddress->AddressDetail,
                'cityName' => (string) $customer->BillingAddress->City,
                'countryId' => getCountry($customer->BillingAddress->Country),
                'postalCode' => (string) $customer->BillingAddress->PostalCode
            );

            $response = updateCustomer($customerToImport);
            if ($response['error'] == null) {
                $customers[$oldCustomerId] = $response;
                echo "Imported customer with ID $oldCustomerId as ".$response['customerId'].'<br/>';
            } else {
                echo "Error inserting customer with ID $oldCustomerId:<br/>";
                echo 'Error code '.$response['error']['code'].': '.$response['error']['reason'].'<br/>';
            }
        }
    }

    foreach($auditFile->MasterFiles->Product as $product) {
        $oldProductCode = (int) $product->ProductCode;

        $existingProduct = getObject('Product', 'productDescription', $product->ProductDescription);

        if ($existingProduct) {
            $products[$oldProductCode] = json_decode($existingProduct, true);
            echo "Product with code $oldProductCode already in current database with code ".$products[$oldProductCode]['productCode'].'<br/>';
        } else {
            $productInfo = getProductInfo((int)$product->ProductCode, $auditFile);
            if ($productInfo == null) {
                continue; // not a valid product, skip
            }
            $productToImport = array(
                'productDescription' => (string) $product->ProductDescription,
                'unitPrice' => $productInfo['unitPrice'],
                'unitOfMeasure' => $productInfo['unitOfMeasure']
            );

            $response = updateProduct($productToImport);
            if ($response['error'] == null) {
                $products[$oldProductCode] = $response;
                echo "Imported product with code $oldProductCode as ".$response['productCode'].'<br/>';
            } else {
                echo "Error inserting product with code $oldProductCode:<br/>";
                echo 'Error code '.$response['error']['code'].': '.$response['error']['reason'].'<br/>';
            }
        }
    }

    foreach($auditFile->MasterFiles->TaxTable->TaxTableEntry as $tax) {
        $taxType = (string) $tax->TaxType;
        $search = new EqualSearch('Tax', 'taxType', array($taxType), array('taxId'));
        $results = $search->getResults();
        if( !$results[0] ) { // tax doesn't exist in the DB
            $newTax = array(
                'taxType' =>  (string) $tax->TaxType,
                'taxPercentage' =>  (float) $tax->TaxPercentage,
                'taxDescription' => (string) $tax->Description
            );
            new Insert('Tax', $newTax);
        }
    }

    foreach($auditFile->SourceDocuments->SalesInvoices->Invoice as $invoice) {
        $lines = array();
        foreach($invoice->Line as $line) {
            $importedLine = array(
                'lineNumber' => (int) $line->LineNumber,
                'productCode' => $products[(int)$line->ProductCode]['productCode'],
                'quantity' => (int) $line->Quantity,
                'unitPrice' => (float) $line->UnitPrice,
                'creditAmount' => (float) $line->CreditAmount,
                'tax' => array(
                    'taxType' => (string) $line->Tax->TaxType,
                    'taxPercentage' => (float) $line->Tax->TaxPercentage
                )
            );
            array_push($lines, $importedLine);
        }

        $invoiceToImport = array(
            'invoiceDate' => (string) $invoice->InvoiceDate,
            'customerId' => $customers[(int)$invoice->CustomerID]['customerId'],
            'line' => $lines,
            'documentTotals' => array(
                'taxPayable' => (float) $invoice->DocumentTotals->TaxPayable,
                'netTotal' => (float) $invoice->DocumentTotals->NetTotal,
                'grossTotal' => (float) $invoice->DocumentTotals->GrossTotal
            )
        );

        $response = updateInvoice($invoiceToImport);
        if ($response['error']) {
            echo "Error inserting product with number $invoice->InvoiceNo:<br/>";
            echo 'Error code '.$response['error']['code'].': '.$response['error']['reason'].'<br/>';
        } else {
            echo "Imported invoice with number $invoice->InvoiceNo as ".$response['invoiceNo'].'<br/>';
        }
    }
}

function getProductInfo($productCode, $auditFile) {
    foreach($auditFile->SourceDocuments->SalesInvoices->Invoice as $invoice) {
        foreach($invoice->Line as $line) {
            if ($productCode == (int)$line->ProductCode) {
                $productInfo = array();
                $productInfo['unitPrice'] = (float) $line->UnitPrice;
                $productInfo['unitOfMeasure'] = (string) $line->UnitOfMeasure;
                return $productInfo;
            }
        }
    }
    return null;
}

function getObject($table, $field, $value) {
    $search = new EqualSearch($table, $field, array($value), array('*'));
    $results = $search->getResults();
    if(!$results[0]) {
        return null;
    }
    $results = json_encode($results[0]);
    return $results;
}

function getCountry($countryCode) {
    $countrySearch = new EqualSearch('Country', 'countryCode', array($countryCode), array('countryId'));
    $results = $countrySearch->getResults();
    if (!$results[0]) {
        // got no results, insert country into database
        new Insert('Country', array('countryName' => $countryCode.'land', 'countryCode' => $countryCode));
        $countrySearch = new EqualSearch('Country', 'countryCode', array($countryCode), array('countryId'));
        $insertedCountry = $countrySearch->getResults();
        if(isSet($insertedCountry[0])) {
            return $insertedCountry[0]['countryId'];
        }
        return null;
    }
    return $results[0]['countryId'];
}

?>