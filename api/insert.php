<?php

class Insert {
    protected $table;
    protected $fields;
    protected $values;
    protected $db;
    protected $sql;

    public function executeQuery() {
        $query = $this->db->prepare($this->sql);
        $query->execute();
    }

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
        $this->db = new PDO("sqlite:../database.db");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->sql = "INSERT INTO $this->table $this->fields VALUES $this->values";

        $this->executeQuery();
    }
} 