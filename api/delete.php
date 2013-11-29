<?php

include_once 'error.php';

class Delete {
    protected $table;
    protected $fieldsAndValues;
    protected $db;
    protected $sql;

    public function executeQuery() {
        $query = $this->db->prepare($this->sql);
        $query->execute();
    }

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