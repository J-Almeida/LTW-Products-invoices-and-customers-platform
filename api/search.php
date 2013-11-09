<?php

class InvalidSearch extends Exception {}

class Search {
    protected $table;
    protected $field;
    protected $values;

    public function getResults() {}
}

class RangeSearch extends Search {
    public function __construct($table, $field, $values) {
        $this->table = $table;
        $this->field = $field;
        if ( count($values) != 2 )
            throw new InvalidSearch();
        $this->values = $values;
    }

    public function getResults() {
        $db = new PDO('sqlite:../database.db');

        $query = $db->query("SELECT id FROM $this->table WHERE $this->field
                                BETWEEN $this->values[0] AND $this->values[1]");

        return $query;
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