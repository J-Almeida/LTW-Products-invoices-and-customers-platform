<?php

function getSearchPage($title, $fields) {
    $html = file_get_contents("searches.html");

    $tableFields = "";
    foreach($fields as $field => $fieldName){
        $tableFields .= " \"$field\" : \"$fieldName\", ";
    }
    $tableFields = substr($tableFields, 0, -1); // remove the last comma

    $searchFields = "";
    foreach($fields as $field => $searchField){
        $searchFields .= "<option value=\" $field \">$searchField</option>";
    };

    $html = str_replace("{{title}}", $title, $html);
    $html = str_replace("{{tableFields}}", $tableFields, $html);
    $html = str_replace("{{searchFields}}", $searchFields, $html);

    return $html;
}