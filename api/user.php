<?php
require_once 'search.php';
require_once 'utilities.php';
require_once 'update.php';
require_once 'insert.php';

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
    $table = 'User';
    $field = 'username';
    $username = $userInfo['username'];
    new Update($table, $userInfo, $field, $username);

    return getUser($username);
}

function insertUser($userInfo) {
    $table = 'User';
    $field = 'username';
    $username = $userInfo['username'];

    new Insert($table, $userInfo);

    return getUser($username);
}