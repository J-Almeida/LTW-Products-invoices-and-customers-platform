<?php

class Query {
    static public $db;
    protected $sql;

    public function executeQuery() {
        try {
            $query = $this->getDB()->prepare($this->sql);
            return $query->execute();
        } catch (Exception $sqlError) {
            $error = new Error($sqlError->getCode(), $sqlError->getMessage());
            die ( json_encode($error->getInfo()) );
        }
    }

    public function getResults() {
        $query = $this->getDB()->prepare($this->sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    static private function getDatabaseName() {
        $db = 'sqlite:';
        $db .= realpath(dirname(__FILE__));
        $db = substr($db, 0, strpos($db, 'api'));
        $db .= 'database.db';
        return $db;
    }

    static public function initializePDO() {
        Query::$db = new PDO(Query::getDatabaseName());
        Query::$db->query('PRAGMA foreign_keys = ON');
        Query::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    static public function getDB() {
        if (Query::$db == null) {
            Query::initializePDO();
        }
        return Query::$db;
    }

    public function quote($a){
        return $this->getDB()->quote($a);
    }
}