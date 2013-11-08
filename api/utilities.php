<?php

// This will retrieve all GET parameters with the same name
// example: api/getProduct?value=1&value=2
// retrieveGETparameters('value') will return [1, 2]
function retrieveGETparameters($parameter) {
    $query  = explode('&', $_SERVER['QUERY_STRING']);
    $params = array();

    foreach( $query as $param )
    {
        list($name, $value) = explode('=', $param);
        if ($name == $parameter)
            $params[urldecode($name)][] = urldecode($value);
    }

    return $params;
}

?>