<?php
require_once 'search.php';
require_once 'utilities.php';
require_once 'update.php';
require_once 'insert.php';

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
    $optionalFields = array('UnitPrice', 'UnitOfMeasure');
    checkFields($productInfo, $obligatoryFields, $optionalFields);

    if ($productCode == NULL) {
        $productCode = getLastProductCode() + 1;
        $productInfo['ProductCode'] = $productCode;
        new Insert('Product', $productInfo);
    } else
        new Update($table, $productInfo, $field, $productCode);

    return getProduct($productCode);
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