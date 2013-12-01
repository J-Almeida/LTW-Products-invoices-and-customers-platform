<?php

include_once 'search.php';

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
    $table = $parameters['table'];
    $field = $parameters['field'];
    $values = $parameters['values'];
    $operation = $parameters['operation'];
    $rows = $parameters['rows'];
    $joins = $parameters['joins'];

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
   $db = new PDO("sqlite:../database.db");
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

function getCurrentPageUrl() {
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == "on")) {$pageURL .= "s";}
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

function roundMoneyAmount(&$amount) {
    $amount = round($amount, 2, PHP_ROUND_HALF_UP);
}

function roundDocumentTotals(&$invoice) {
    roundMoneyAmount($invoice['taxPayable']);
    roundMoneyAmount($invoice['netTotal']);
    roundMoneyAmount($invoice['grossTotal']);
}

function roundLineTotals(&$line) {
    roundMoneyAmount($line['unitPrice']);
    roundMoneyAmount($line['creditAmount']);
}

function roundProductTotals(&$product) {
    roundMoneyAmount($product['unitPrice']);
}

function getId($table, $field, $value) {
    $table = lcfirst($table);
    $values = array($value);
    $rows = array($table.'Id');
    $invoiceSearch = new EqualSearch($table, $field, $values, $rows);
    return $invoiceSearch->getResults()[0][$table.'Id'];
}

function getAPIUrl($table, $field, $value) {
    $url = getCurrentPageUrl();
    $url = substr($url, 0, strpos($url, 'api/'));
    $url .= "/api/get$table.php?$field=";
    $url .= urlencode($value);
    return $url;
}

function http_post($url, $data, $headers=null) {

    $data = http_build_query($data);
    $opts = array('http' => array('method' => 'POST', 'content' => $data));

    if($headers) {
        $opts['http']['header'] = $headers;
    }
    $st = stream_context_create($opts);
    $fp = fopen($url, 'rb', false, $st);

    if(!$fp) {
        return false;
    }
    return stream_get_contents($fp);
}