<?php

include_once 'error.php';
include_once 'query.php';

class Delete extends Query {
    protected $table;
    protected $fieldsAndValues;

    public function setFieldsAndValues($fieldsAndValues) {
        $this->fieldsAndValues = "";
        foreach($fieldsAndValues as $field => $value) {
            $this->fieldsAndValues .= $field . ' = ' . "'$value' AND ";
        }
        $this->fieldsAndValues = rtrim($this->fieldsAndValues, "AND ");
    }

    public function __construct($table, $fieldsAndValues) {
        $this->table = $table;
        $this->setFieldsAndValues($fieldsAndValues);
        $this->db = new PDO("sqlite:../database.db");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->sql = "DELETE FROM $this->table WHERE $this->fieldsAndValues";

        $this->executeQuery();
    }

} 