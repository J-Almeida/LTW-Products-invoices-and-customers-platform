<?php

class InvalidSearch extends Exception {
    private $info;

    public function __construct($errorCode, $reason) {
        $errorInfo = array();
        $errorInfo['code'] = $errorCode;
        $errorInfo['reason'] = $reason;
        $this->info = array('error' => $errorInfo);
    }

    public function getInfo() {
        return $this->info;
    }
}

class Search {
    protected $table;
    protected $field;
    protected $values;
    protected $joins;
    protected $rows;
    protected $db;

    public function getResults() {}

    protected function initialize($table, $field, $values, $rows, $tableJoints) {
        $this->table = $table;
        $this->field = $field;
        $this->values = $values;
        $this->setRows($rows);
        $this->setJoints($tableJoints);
        $this->db = new PDO("sqlite:../database.db");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    // this will create the necessary table joins string to be in the sql query
    protected function setJoints($tableJoints) {
        if (count($tableJoints) == 0)
            $this->joins = "";
        else
            foreach ($tableJoints as $table => $joins ) {
                if ( is_array($joins) )  // support for multiple joins on the same table
                    foreach ($joins as $join)
                        $this->joins .= "INNER JOIN $join ON $table.$join" . "Id" . " = $join.$join" . "Id ";
                else
                    $this->joins .= "INNER JOIN $joins ON $table.$joins" . "Id" . " = $joins.$joins" . "Id ";
            }
    }

    // this will create the table rows string that will be selected in the sql query
    protected function setRows($rows) {
        if (count($rows) == 0)
            $this->rows = ' * '; // no rows specified returns all rows
        else{
            $this->rows .= $rows[0];
            for($i = 1; $i < count($rows); $i++) {
                $this->rows .= ", $rows[$i]";
            }
        }
    }
}

class RangeSearch extends Search {
    public function __construct($table, $field, $values, $rows, $tableJoints = array()) {
        if ( count($values) != 2 )
            throw new InvalidSearch(700, "Expected only 2 values");
        $this->initialize($table, $field, $values, $rows, $tableJoints);
    }

    public function getResults() {
        $value1 = $this->values[0]; $value2 = $this->values[1];
        $query = $this->db->query("SELECT $this->rows FROM $this->table $this->joins WHERE $this->field BETWEEN '$value1' AND '$value2'");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}

class EqualSearch extends Search {
    public function __construct($table, $field, $values, $rows, $tableJoints = array()) {
        if ( count($values) != 1 )
            throw new InvalidSearch(700, "Expected only 1 value");
        $this->initialize($table, $field, $values, $rows, $tableJoints);
    }

    public function getResults() {
        $value = $this->values[0];
        $query = $this->db->query("SELECT $this->rows FROM $this->table $this->joins WHERE $this->field = '$value'");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}

class ContainsSearch extends Search {
    public function __construct($table, $field, $values, $rows, $tableJoints = array()) {
        if ( count($values) != 1 )
            throw new InvalidSearch(700, "Expected only 1 value");
        $this->initialize($table, $field, $values, $rows, $tableJoints);
    }

    public function getResults() {
        $value = $this->values[0];
        $query = $this->db->query("SELECT $this->rows FROM $this->table $this->joins WHERE $this->field LIKE ('%$value%')");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}

class MinSearch extends Search {
    public function __construct($table, $field, $values, $rows, $tableJoints = array()) {
        if ( count($values) != 0 )
            throw new InvalidSearch(700, "Expected no values");
        $this->initialize($table, $field, $values, $rows, $tableJoints);
    }

    public function getResults() {
        $query = $this->db->query("SELECT $this->rows from $this->table $this->joins WHERE $this->field = (SELECT min($this->field) FROM $this->table)" );
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}

class MaxSearch extends Search {
    public function __construct($table, $field, $values, $rows, $tableJoints = array()) {
        if ( count($values) != 0 )
            throw new InvalidSearch(700, "Expected no values");
        $this->initialize($table, $field, $values, $rows, $tableJoints);
    }

    public function getResults() {
        $query = $this->db->query("SELECT $this->rows from $this->table $this->joins WHERE $this->field = (SELECT max($this->field) FROM $this->table)" );
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}