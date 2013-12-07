<?php
require_once 'search.php';
require_once 'utilities.php';
require_once 'update.php';

function getProduct($productCode) {
    // Fetch the product we are looking for
    $table = 'Product';
    $field = 'productCode';
    $values = array($productCode);
    $rows = array('productCode','productDescription', 'unitPrice', 'unitOfMeasure');
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

// TODO select only the necessary fields from the json, return error when important fields are missing

    $table = 'Product';
    $field = 'productCode';
    $productCode = $productInfo['productCode'];
    if ($productCode == NULL) {
        $productCode = getLastProductCode() + 1;
        $productInfo['productCode'] = $productCode;
        new Insert('Product', $productInfo);
    } else
        new Update($table, $productInfo, $field, $productCode);

    return getProduct($productCode);
}

function getLastProductCode(){
    $table = 'Product';
    $field = 'productCode';
    $values = array();
    $rows = array('productCode');
    $max = new MaxSearch($table, $field, $values, $rows);
    $results = $max->getResults();
    if(isSet($results[0])) {
        return $results[0]['productCode'];
    }
    return 0;
}