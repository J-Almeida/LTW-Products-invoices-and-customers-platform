<?php

include_once 'query.php';

class Update extends Query {
    protected $table;
    protected $updatedInfo;
    protected $field;
    protected $value;

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
        $this->sql = "UPDATE $this->table SET $this->updatedInfo WHERE $this->field = '" . $this->value . "'";

        $this->executeQuery();
    }

} 