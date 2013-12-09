<?php
require_once 'search.php';

function getSearchParametersFromURL() {
    $field = NULL;
    if ( isset($_GET['field']) && !empty($_GET['field']) ) {
        $field = $_GET['field'];
    } else {
        $error = new Error(700, 'Missing \'field\' field');
        die( json_encode($error->getInfo()) );
    }

    $values = array();
    if ( isset($_GET['value']) && !empty($_GET['value']) ) {
        $values = $_GET['value'];
    }

    $operation = NULL;
    if ( isset($_GET['op']) && !empty($_GET['op']) ) {
        $operation = $_GET['op'];
    } else {
        $error = new Error(700, 'Missing \'op\' field');
        die( json_encode($error->getInfo()) );
    }

    return array('field' => $field, 'values' => $values, 'operation' => $operation);
}

function executeSearch($parameters) {
    if(isset($parameters['table']))
        $table = $parameters['table'];
    $field = $parameters['field'];
    if(isset($parameters['values']))
        $values = $parameters['values'];
    else
        $values = array();
    $operation = $parameters['operation'];
    $rows = $parameters['rows'];
    if(isset($parameters['joins']))
        $joins = $parameters['joins'];
    else
        $joins = array();

    try {
        $reflection = new ReflectionClass($operation.'search');
        $variables = array($table, $field, $values, $rows, $joins);
        $search = $reflection->newInstanceArgs($variables);
        $result = $search->getResults();
        return $result;
    } catch (ReflectionException $exception) {
        $error = new Error(700, 'Invalid \'op\' field');
        die( json_encode($error->getInfo()) );
    } catch (Error $invalid) {
        die( json_encode($invalid->getInfo()) );
    } catch (Exception $sqlError) {
        $error = new Error($sqlError->getCode(), $sqlError->getMessage());
        die ( json_encode($error->getInfo()) );
    }

    return NULL;
}

// Takes values from an array, unsets and sets them as a new array of values
// Example: if an array has values 'taxType' and 'taxPercentage' and we want the new value 'tax'
// this function will create a new array with 'taxType' and 'taxPercentage'
// and set that array as an index to array['tax']
function setValuesAsArray($newValue, $values, &$array) {
    $attributesArray = array();
    foreach($values as $value){
        $attributesArray[$value] = $array[$value];
        unset($array[$value]);
    }
    $array[$newValue] = $attributesArray;
}

function itemExists($table, $itemValue, $itemType) {
   $db = new PDO(getDatabase());
   $query = $db->query("SELECT * FROM $table WHERE $itemType = '$itemValue'");
   $query->setFetchMode(PDO::FETCH_ASSOC);
   $result = $query->fetch();
   if($result[$itemType]) {
     return true;
   } 
   else {
     return false;
   }
}

function roundMoneyAmount(&$amount) {
    $amount = round($amount, 2);
}

function roundDocumentTotals(&$invoice) {
    roundMoneyAmount($invoice['TaxPayable']);
    roundMoneyAmount($invoice['NetTotal']);
    roundMoneyAmount($invoice['GrossTotal']);
}

function roundLineTotals(&$line) {
    roundMoneyAmount($line['UnitPrice']);
    roundMoneyAmount($line['CreditAmount']);
}

function roundProductTotals(&$product) {
    roundMoneyAmount($product['UnitPrice']);
}

function getId($table, $field, $value) {
    $values = array($value);
    $rows = array($table.'ID');
    $search = new EqualSearch($table, $field, $values, $rows);
    $results = $search->getResults();
    if(isSet($results[0])) {
        return $results[0][$table.'ID'];
    }
    return null;
}

function getAllPermissions() {
    $parameters = array('field' => '', 'operation' => "listall");
    $parameters['table'] = 'Permission';
    $parameters['rows'] = array('permissionId', 'permissionType');
    $parameters['joins'] = array();

    $result = executeSearch($parameters);
    return $result;
}

function getDatabase() {
    $db = 'sqlite:';
    $db .= realpath(dirname(__FILE__));
    $db = substr($db, 0, strpos($db, 'api'));
    $db .= 'database.db';
    return $db;
}

function getStartDate() {
    $invoiceSearch = new MinSearch('Invoice', 'InvoiceDate', array(), array('*'));
    $results = $invoiceSearch->getResults();

    return $results[0]['InvoiceDate'];
}

function getEndDate() {
    $invoiceSearch = new MaxSearch('Invoice', 'InvoiceDate', array(), array('*'));
    $results = $invoiceSearch->getResults();

    return $results[0]['InvoiceDate'];
}

function getFiscalYear() {
    $startDate = createFromFormat("Y-m-d", getStartDate());
    return $startDate->format("Y");
}

// Checks if all obligatory fields are missing or empty in a json info array
// deletes all fields which are neither obligatory nor optional
function validateFields(&$info, $obligatoryFields, $optionalFields = array()) {

    foreach($obligatoryFields as $field) {
        if ( !isset($info[$field]) ) {
            $error = new Error(700, "Missing field $field");
            die( json_encode($error->getInfo()) );
        }

        if ( empty($info[$field]) ) {
            $error = new Error(700, "Empty field $field");
            die( json_encode($error->getInfo()) );
        }

        validateField($field, $info);
    }

    foreach($optionalFields as $field) {
        if ( array_key_exists($field, $info) )
            validateField($field, $info);
    }

    foreach($info as $field => $value) {
        if ( !in_array($field, $obligatoryFields) && !in_array($field, $optionalFields) ) {
            unset($info[$field]);
        }
    }
}

function validateField($field, &$info) {
    require_once 'inputValidators.php';

    $validations = array(
        'CompanyName'   =>  'isValidTextField',
        'CustomerTaxID' =>  'isValidPositiveNumber',
        'Email'         =>  'isValidEmail',
        'AddressDetail' =>  'isValidLargeTextField',
        'City'          =>  'isValidTextField',
        'PostalCode'    =>  'isValidTextField',
        'Country'       =>  'isValidTextField',
        'CountryName'   =>  'isValidTextField',
        'InvoiceNo'     =>  'isValidInvoiceNo',
     // 'InvoiceDate'   =>  '',
        'Quantity'      =>  'isValidPositiveNumber',
        'TaxPercentage' =>  'isValidPositiveNumber',
        'UnitPrice'     =>  'isValidPositiveNumber'
    );

    if (array_key_exists($field, $validations)) {
        $valid = call_user_func($validations[$field], $info[$field]);
        if (!$valid) {
            $error = new Error(700, "Invalid field $field");
            die(json_encode($error->getInfo()));
        }
    }
}