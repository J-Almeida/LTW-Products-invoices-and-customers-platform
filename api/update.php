<?php

class Update {
    protected $table;
    protected $updatedInfo;
    protected $field;
    protected $value;
    protected $db;
    protected $sql;

    public function executeQuery() {
        $query = $this->db->prepare($this->sql);
        $query->execute();
    }

    public function setUpdatedInfo($updatedInfoArray) {
        $this->updatedInfo = "";
        foreach ($updatedInfoArray as $row => $info ) {
            $this->updatedInfo .= $row . ' = ' . "'$info', ";
        }
        $this->updatedInfo = rtrim($this->updatedInfo, ", ");
    }

    public function __construct($table, $updatedInfoArray, $field, $value) {
        $this->table = $table;
        $this->setUpdatedInfo($updatedInfoArray);
        $this->field = $field;
        $this->value = $value;
        $this->db = new PDO("sqlite:../database.db");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->sql = "UPDATE $this->table SET $this->updatedInfo WHERE $this->field = '" . $this->value . "'";

        $this->executeQuery();
    }

} 