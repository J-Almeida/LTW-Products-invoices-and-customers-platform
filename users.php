<?php

include_once "searches.php";

$fields = array(
    'username' => 'Username',
    'name' => 'Full name',
    'email' => 'Email',
    'permissionType' => 'Permission type');

echo getSearchPage("Users", $fields);