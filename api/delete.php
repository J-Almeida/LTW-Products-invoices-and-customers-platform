<?php

require_once 'error.php';
require_once 'query.php';

class Delete extends Query {
    protected $table;
    protected $fieldsAndValues;

    public function setFieldsAndValues($fieldsAndValues) {
        $this->fieldsAndValues = "";
        foreach($fieldsAndValues as $field => $value) {
            $this->fieldsAndValues .= $field . ' = ' . $this->quote($value) . " AND ";
        }
        $this->fieldsAndValues = rtrim($this->fieldsAndValues, "AND ");
    }

    public function __construct($table, $fieldsAndValues) {
        $this->table = $table;
        if (count($fieldsAndValues) == 0) {
            $this->sql = "DELETE FROM $this->table";
        } else {
            $this->setFieldsAndValues($fieldsAndValues);
            $this->sql = "DELETE FROM $this->table WHERE $this->fieldsAndValues";
        }
        $this->executeQuery();
    }

} 