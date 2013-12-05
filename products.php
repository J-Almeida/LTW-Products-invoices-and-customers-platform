<?php
session_start();
require_once "searches.php";
require_once './api/authenticationUtilities.php';

$neededPermissions = array('read');
evaluateSessionPermissions($neededPermissions);

$fields = array(
    'productCode' => 'Product Code',
    'productDescription' => 'Description',
    'unitPrice' => 'Unit Price',
    'unitOfMeasure' => 'Units Of Measure');

echo getSearchPage("Products", $fields);