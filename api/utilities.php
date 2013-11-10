<?php

// This will retrieve all GET parameters with the same name
// example: api/getProduct?value=1&value=2
// retrieveGETparameter('value') will return [1, 2]
function retrieveGETparameter($parameter) {
    $query  = explode('&', $_SERVER['QUERY_STRING']);
    $params = array();

    foreach( $query as $param )
    {
        list($name, $value) = explode('=', $param);
        if ($name == $parameter)
            $params[] = urldecode($value);
    }

    return $params;
}


function getSearchParametersFromURL() {
    $field = NULL;
    if ( isset($_GET['field']) && !empty($_GET['field']) ) {
        $field = $_GET['field'];
    } else {
        $error = new InvalidSearch(700, 'Missing \'field\' field');
        die( json_encode($error->getInfo()) );
    }

    $values = array();
    if ( isset($_GET['value']) && !empty($_GET['value']) ) {
        $values = retrieveGETparameter('value');
    }

    $operation = NULL;
    if ( isset($_GET['op']) && !empty($_GET['op']) ) {
        $operation = $_GET['op'];
    } else {
        $error = new InvalidSearch(700, 'Missing \'op\' field');
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
        $error = new InvalidSearch(700, 'Invalid \'op\' field');
        die( json_encode($error->getInfo()) );
    } catch (InvalidSearch $invalid) {
        die( json_encode($invalid->getInfo()) );
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