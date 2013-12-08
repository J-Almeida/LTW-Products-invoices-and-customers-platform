<?php
require_once 'bootstrap.php';
require_once "searches.php";
require_once './api/authenticationUtilities.php';

$neededPermissions = array('promote');
evaluateSessionPermissions($neededPermissions);

$fields = array(
    'username' => 'Username',
    'name' => 'Full name',
    'email' => 'Email',
    'permissionType' => 'Permission type');

echo getSearchPage("Users", $fields);