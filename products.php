<?php
require_once 'bootstrap.php';
require_once "searches.php";
require_once './api/authenticationUtilities.php';

$neededPermissions = array('read');
evaluateSessionPermissions($neededPermissions);

$fields = array(
    'ProductCode' => 'Product Code',
    'ProductDescription' => 'Description',
    'UnitPrice' => 'Unit Price',
    'UnitOfMeasure' => 'Units Of Measure');

echo getSearchPage("Products", $fields);