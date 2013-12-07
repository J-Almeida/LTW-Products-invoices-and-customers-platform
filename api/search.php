<?php

require_once 'error.php';
require_once 'query.php';

class Search extends Query {
    protected $table;
    protected $field;
    protected $values;
    protected $joins;
    protected $rows;

    protected function initialize($table, $field, $values, $rows, $tableJoins) {
        $this->table = $table;
        $this->field = $field;
        $this->values = $values;
        $this->setRows($rows);
        $this->setJoins($tableJoins);
    }

    // this will create the necessary table joins string to be in the sql query
    protected function setJoins($tableJoins) {
        if (count($tableJoins) == 0)
            $this->joins = "";
        else
            foreach ($tableJoins as $table => $joins ) {
                if ( is_array($joins) )  // support for multiple joins on the same table
                    foreach ($joins as $join)
                        $this->joins .= "INNER JOIN $join ON $table.$join" . "ID" . " = $join.$join" . "ID ";
                else
                    $this->joins .= "INNER JOIN $joins ON $table.$joins" . "ID" . " = $joins.$joins" . "ID ";
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
    public function __construct($table, $field, $values, $rows, $tableJoins = array()) {
        if ( count($values) != 2 )
            throw new Error(700, "Expected only 2 values");
        $this->initialize($table, $field, $values, $rows, $tableJoins);
        $this->sql = "SELECT $this->rows FROM $this->table $this->joins WHERE $this->field BETWEEN '". $this->values[0] . "' AND '" . $this->values[1] . "'";
    }
}

class EqualSearch extends Search {
    public function __construct($table, $field, $values, $rows, $tableJoins = array()) {
        if ( count($values) != 1 )
            throw new Error(700, "Expected only 1 value");
        $this->initialize($table, $field, $values, $rows, $tableJoins);
        $this->sql = "SELECT $this->rows FROM $this->table $this->joins WHERE $this->field = '" . $this->values[0] . "'";
    }
}

class ContainsSearch extends Search {
    public function __construct($table, $field, $values, $rows, $tableJoins = array()) {
        if ( count($values) != 1 )
            throw new Error(700, "Expected only 1 value");
        $this->initialize($table, $field, $values, $rows, $tableJoins);
        $this->sql = "SELECT $this->rows FROM $this->table $this->joins WHERE $this->field LIKE ('%" . $this->values[0] ."%')";
    }
}

class MinSearch extends Search {
    public function __construct($table, $field, $values, $rows, $tableJoins = array()) {
        if ( count($values) != 0 )
            throw new Error(700, "Expected no values");
        $this->initialize($table, $field, $values, $rows, $tableJoins);
        $this->sql = "SELECT $this->rows from $this->table $this->joins WHERE $this->field = (SELECT min($this->field) FROM $this->table $this->joins)";
    }
}

class MaxSearch extends Search {
    public function __construct($table, $field, $values, $rows, $tableJoins = array()) {
        if ( count($values) != 0 )
            throw new Error(700, "Expected no values");
        $this->initialize($table, $field, $values, $rows, $tableJoins);
        $this->sql = "SELECT $this->rows from $this->table $this->joins WHERE $this->field = (SELECT max($this->field) FROM $this->table $this->joins)";
    }
}

class ListAllSearch extends Search {
    public function __construct($table, $field, $values, $rows, $tableJoins = array()) {
        if ( count($values) != 0 )
            throw new Error(700, "Expected no values");
        $this->initialize($table, $field, $values, $rows, $tableJoins);
        $this->sql = "SELECT $this->rows from $this->table $this->joins";
    }
}