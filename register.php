<?php
require_once 'bootstrap.php';
require_once './api/insert.php';
require_once './api/authenticationUtilities.php';

header('Refresh: 2; URL=./index.php');

echo "<link rel=\"stylesheet\" href=\"style.css\">";
echo "<br><br><br>";

//Don't let register a new user if there's currently a logged in one
if(isset($_SESSION['username'])) {
	echo "<p style='text-align: center;'>Log off before registering a new user.</p>";
	echo "<p style='text-align: center;'>Redirecting... </p>";
	exit;
}

//Check if all the credentials have been sent
if( !isset($_POST["username"]) || !isset($_POST["name"]) || !isset($_POST["password"]) || !isset($_POST["email"]) ) {
	echo "<p style='text-align: center;'>Please register using the correct form, thank you!</p>";
	echo "<p style='text-align: center;'>Redirecting... </p>";
	exit;
}

$login = $_POST["username"];
$email = $_POST["email"];
$password = $_POST["password"];

//Verify if a user already exists with that username or that email and exit if so
$existingUsername = getUsername($login);
$existingEmail = getUsername($email);
if( (isset($existingUsername) && !empty($existingUsername)) || (isset($existingEmail) && !empty($existingEmail)) ) {
	echo "<p style='text-align: center;'>A user with that username or email already exists. </p>";
	echo "<p style='text-align: center;'>Redirecting... </p>";
	exit;
}

//Insert the new user to the database, with default reader permission
$fields = array(
	'username'         => $_POST["username"],
	'name'             => $_POST["name"],
	'password'         => $_POST["password"],
	'email'            => $_POST["email"],
	'permissionId'     => 3
	);

$insertedLines = new Insert('User', $fields);

//Login the new user automatically
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