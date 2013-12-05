<?php
session_start();
include_once "searches.php";
include_once './api/authenticationUtilities.php';

$neededPermissions = array('promote');
evaluateSessionPermissions($neededPermissions);

$fields = array(
    'username' => 'Username',
    'name' => 'Full name',
    'email' => 'Email',
    'permissionType' => 'Permission type');

echo getSearchPage("Users", $fields);