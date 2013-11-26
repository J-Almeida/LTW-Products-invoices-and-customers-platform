<?php

class Error extends Exception {
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