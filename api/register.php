<?php
	include_once 'insert.php';

	$fields = array(
        'username'         => $_POST["username"],
        'name'             => $_POST["name"],
        'password'     => $_POST["password"],
        'email'        => $_POST["email"],
        'permissionId' => 3
    );

    $insertedLines = new Insert('User', $fields);
    var_dump($insertedLines);
?>