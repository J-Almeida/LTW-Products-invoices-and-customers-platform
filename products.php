<?php

include "searches.php";

$fields = array(
    'productCode' => 'Product Code',
    'productDescription' => 'Description',
    'unitPrice' => 'Unit Price',
    'unitOfMeasure' => 'Units Of Measure');

echo getSearchPage("Products", $fields);