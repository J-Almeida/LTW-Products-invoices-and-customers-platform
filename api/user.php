<?php
require_once 'search.php';
require_once 'utilities.php';
require_once 'update.php';

function getUser($userName) {
// Fetch the user we are looking for
    $table = 'User';
    $field = 'username';
    $values = array($userName);
    $rows = array('username', 'name', 'email', 'permissionType');
    $joins = array('User' => 'Permission');

    $search = new EqualSearch($table, $field, $values, $rows, $joins);
    $result = $search->getResults();

    if (!$result) {
        $error = new Error(404, "User not found");
        return $error->getInfo();
    }

    $result = $result[0];

    return $result;
}

function updateUser($userInfo) {
    // TODO select only the necessary fields from the json, return error when important fields are missing

    $table = 'User';
    $field = 'username';
    $username = $userInfo['username'];
    new Update($table, $userInfo, $field, $username);

    return getUser($username);
}