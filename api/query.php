<?php

class Query {
    protected $db;
    protected $sql;

    public function executeQuery() {
        try {
            $this->db = new PDO("sqlite:../database.db");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $query = $this->db->prepare($this->sql);
            return $query->execute();
        } catch (Exception $sqlError) {
            $error = new Error($sqlError->getCode(), $sqlError->getMessage());
            die ( json_encode($error->getInfo()) );
        }
    }

    public function getResults() {
        $this->db = new PDO("sqlite:../database.db");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = $this->db->prepare($this->sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
} 