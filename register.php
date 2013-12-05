<?php
session_start();
include_once './api/insert.php';
include_once './api/authenticationUtilities.php';

$fields = array(
	'username'         => $_POST["username"],
	'name'             => $_POST["name"],
	'password'         => $_POST["password"],
	'email'            => $_POST["email"],
	'permissionId'     => 3
	);

$insertedLines = new Insert('User', $fields);

$login = $_POST["username"];
$password = $_POST["password"];
header('Refresh: 1; URL=./index.php');

echo "<link rel=\"stylesheet\" href=\"style.css\">";
echo "<br><br><br>";

if(checkPassword($login, $password)) {
	$user = getUsername($login);
	$permissions = getPermissions($user);
	$_SESSION['username'] = $user;
	$_SESSION['permissions'] = $permissions;
	echo "<p style='text-align: center;'>Thank you for registering!</p>";
	echo "<p style='text-align: center;'>Welcome " . $user . "</p>";
}
else
	echo "<p style='text-align: center;'>Wrong username or password. </p>";

echo "<p style='text-align: center;'>Redirecting... </p>";
exit;
?>