<?php
	include_once 'insert.php';

	$fields = array(
        'username'         => $_POST["username"],
        'name'             => $_POST["name"],
        'userPassword'     => $_POST["password"],
        'userEmail'        => $_POST["email"],
        'userPermissionId' => 3
    );

    $insertedLines = new Insert('User', $fields);
    var_dump($insertedLines);
?>