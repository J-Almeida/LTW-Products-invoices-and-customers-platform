<?php

class InvalidSearch extends Exception {}

class Search {
    protected $table;
    protected $field;
    protected $values;
    protected $joins;
    protected $rows;
    protected static $databaseFile = "sqlite:../database.db";

    public function getResults() {}

    // this will create the necessary table joins string to be in the sql query
    protected function setJoints($tableJoints) {
        if (count($tableJoints) == 0)
            $this->joins = "";
        else
            foreach ($tableJoints as $join ) {
                $this->joins .= "INNER JOIN $join ON $this->table.$join" . "Id" . " = $join.$join" . "Id ";
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
        $this->table = $table;
        $this->field = $field;
        if ( count($values) != 2 )
            throw new InvalidSearch();
        $this->values = $values;
        $this->setRows($rows);
        $this->setJoints($tableJoints);
    }

    public function getResults() {
        $db = new PDO(parent::$databaseFile);

        $value1 = $this->values[0]; $value2 = $this->values[1];

        $query = $db->query("SELECT $this->rows FROM $this->table $this->joins WHERE $this->field BETWEEN '$value1' AND '$value2'");

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}

class EqualSearch extends Search {

}

class ContainsSearch extends Search {

}

class MinSearch extends Search {

}

class MaxSearch extends Search {

}