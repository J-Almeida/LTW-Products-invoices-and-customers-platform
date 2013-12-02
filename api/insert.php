<?php

include_once 'query.php';

class Insert extends Query {
    protected $table;
    protected $fields;
    protected $values;

    public function setFieldsAndValues($fieldsAndValues) {
        $this->fields = '(';
        $this->values = '(';
        foreach($fieldsAndValues as $field => $value) {
            $this->fields .= "'$field', ";
            $this->values .= "'$value', ";
        }
        $this->fields = rtrim($this->fields, ', ');
        $this->fields .= ')';
        $this->values = rtrim($this->values, ', ');
        $this->values .= ')';
    }

    public function __construct($table, $fieldsAndValues) {
        $this->table = $table;
        $this->setFieldsAndValues($fieldsAndValues);
        $this->sql = "INSERT INTO $this->table $this->fields VALUES $this->values";
        $this->executeQuery();
    }
} 